<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:generate:entities',
    description: 'Generate basic Doctrine entities and repositories from existing database tables',
)]
class GenerateEntitiesCommand extends Command
{
public function __construct(
    private readonly Connection $connection,
    private readonly KernelInterface $kernel,
) {
    parent::__construct();
}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $schemaManager = $this->connection->createSchemaManager();
        $tables = $schemaManager->listTables();

        if (empty($tables)) {
            $io->warning('No tables found in the current database.');
            return Command::SUCCESS;
        }

$projectDir = $this->kernel->getProjectDir();
$entityDir = $projectDir . '/src/Entity';
$repositoryDir = $projectDir . '/src/Repository';

        if (!is_dir($entityDir)) {
            mkdir($entityDir, 0777, true);
        }

        if (!is_dir($repositoryDir)) {
            mkdir($repositoryDir, 0777, true);
        }

        $io->section('Generating Entity Classes from Database...');

        foreach ($tables as $table) {
            $tableName = $table->getName();
            $className = $this->tableToClassName($tableName);

            $columnsCode = [];
            $getterSetterCode = [];

            foreach ($table->getColumns() as $column) {
                $columnName = $column->getName();

                if ($columnName === 'id') {
                    $columnsCode[] = <<<PHP
    #[ORM\\Id]
    #[ORM\\GeneratedValue]
    #[ORM\\Column]
    private ?int \$id = null;

PHP;

                    $getterSetterCode[] = <<<PHP
    public function getId(): ?int
    {
        return \$this->id;
    }

PHP;
                    continue;
                }

                $propertyName = $this->columnToPropertyName($columnName);
                $phpType = $this->dbalTypeToPhpType($column->getType()->getName(), $columnName);
                $doctrineType = $this->dbalTypeToDoctrineType($column->getType()->getName(), $columnName);
                $nullable = $column->getNotnull() ? 'false' : 'true';
                $length = $column->getLength();

                $columnOptions = "type: '{$doctrineType}'";
                if ($length && in_array($doctrineType, ['string'])) {
                    $columnOptions .= ", length: {$length}";
                }
                if ($nullable === 'true') {
                    $columnOptions .= ", nullable: true";
                }

                $nullablePrefix = $nullable === 'true' ? '?' : '';
                $defaultValue = $nullable === 'true' ? ' = null' : '';

                $columnsCode[] = <<<PHP
    #[ORM\\Column({$columnOptions})]
    private {$nullablePrefix}{$phpType} \${$propertyName}{$defaultValue};

PHP;

                $getterSetterCode[] = <<<PHP
    public function get{$this->studly($propertyName)}(): {$nullablePrefix}{$phpType}
    {
        return \$this->{$propertyName};
    }

    public function set{$this->studly($propertyName)}({$nullablePrefix}{$phpType} \${$propertyName}): static
    {
        \$this->{$propertyName} = \${$propertyName};

        return \$this;
    }

PHP;
            }

            $entityCode = <<<PHP
<?php

namespace App\Entity;

use App\Repository\\{$className}Repository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: {$className}Repository::class)]
class {$className}
{
{$this->indent(implode('', $columnsCode), 0)}
{$this->indent(implode('', $getterSetterCode), 0)}
}

PHP;

            $entityPath = $entityDir . '/' . $className . '.php';
            file_put_contents($entityPath, $entityCode);

            $repositoryCode = <<<PHP
<?php

namespace App\Repository;

use App\Entity\\{$className};
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class {$className}Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry \$registry)
    {
        parent::__construct(\$registry, {$className}::class);
    }
}

PHP;

            $repositoryPath = $repositoryDir . '/' . $className . 'Repository.php';
            file_put_contents($repositoryPath, $repositoryCode);

            $io->success("Generated: src/Entity/{$className}.php");
            $io->writeln("Generated repository: App\\Repository\\{$className}Repository");
        }

        $io->note('Basic entities generated successfully. Review relations and adjust types if needed.');
        $io->note('Next: run php bin/console doctrine:migrations:diff then php bin/console doctrine:migrations:migrate');

        return Command::SUCCESS;
    }

    private function tableToClassName(string $tableName): string
    {
        return $this->studly($tableName);
    }

    private function columnToPropertyName(string $columnName): string
    {
        $studly = $this->studly($columnName);
        return lcfirst($studly);
    }

    private function studly(string $value): string
    {
        $value = str_replace(['-', '_'], ' ', strtolower($value));
        $value = ucwords($value);
        return str_replace(' ', '', $value);
    }

    private function dbalTypeToPhpType(string $dbalType, string $columnName): string
    {
        return match ($dbalType) {
            'integer', 'bigint', 'smallint' => 'int',
            'float', 'decimal' => 'float',
            'boolean' => 'bool',
            'date', 'datetime', 'datetimetz', 'datetime_immutable', 'date_immutable' => '\\DateTimeInterface',
            default => 'string',
        };
    }

    private function dbalTypeToDoctrineType(string $dbalType, string $columnName): string
    {
        return match ($dbalType) {
            'integer' => 'integer',
            'bigint' => 'bigint',
            'smallint' => 'smallint',
            'float' => 'float',
            'decimal' => 'decimal',
            'boolean' => 'boolean',
            'text' => 'text',
            'date' => 'date',
            'datetime', 'datetimetz' => 'datetime',
            'datetime_immutable' => 'datetime_immutable',
            'date_immutable' => 'date_immutable',
            default => 'string',
        };
    }

    private function indent(string $text, int $spaces = 4): string
    {
        $indent = str_repeat(' ', $spaces);
        return preg_replace('/^/m', $indent, $text);
    }
}