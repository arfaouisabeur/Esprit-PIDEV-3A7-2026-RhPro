"""
1_build_dataset.py — Dataset CV complets
=========================================
Génère un dataset réaliste basé sur des VRAIES structures de CV :
- Sections Compétences (listes)
- Sections Expérience (phrases longues avec contexte)
- Résumés professionnels
- Certifications, formations
- Phrases négatives (bruit)

~600 exemples annotés couvrant tous les patterns d'un vrai CV.

Usage:
    python 1_build_dataset.py
"""

import spacy
import random
import os
import re
from spacy.util import filter_spans
from spacy.tokens import DocBin

# ─── DICTIONNAIRE ÉTENDU ──────────────────────────────────────────────────────
SKILLS = {
    "langages": [
        "Python", "Java", "JavaScript", "TypeScript", "PHP", "C", "C++", "C#",
        "Ruby", "Go", "Kotlin", "Swift", "Scala", "R", "MATLAB", "Dart",
        "Bash", "Shell", "Rust", "Groovy", "Perl", "Lua", "Assembly",
    ],
    "frontend": [
        "React", "React.js", "Vue.js", "Angular", "Next.js", "Nuxt.js",
        "Svelte", "HTML5", "CSS3", "Bootstrap", "Tailwind CSS", "jQuery",
        "Redux", "Vuex", "Pinia", "GraphQL", "Webpack", "Vite",
        "Jest", "Cypress", "Playwright", "D3.js", "Three.js",
    ],
    "backend": [
        "Django", "Flask", "FastAPI", "Spring Boot", "Laravel", "Symfony",
        "Express.js", "NestJS", "Node.js", "ASP.NET", "Rails", "Phoenix",
        "Gin", "Fiber", "Celery", "RabbitMQ", "Kafka", "gRPC",
        "Django REST Framework", "API Platform", "Hibernate",
    ],
    "mobile": [
        "Flutter", "React Native", "Android", "iOS", "Xamarin", "Ionic",
        "Swift", "Kotlin", "Expo",
    ],
    "databases": [
        "MySQL", "PostgreSQL", "MongoDB", "Oracle", "SQL Server", "SQLite",
        "Redis", "Elasticsearch", "Cassandra", "MariaDB", "Firebase",
        "DynamoDB", "CouchDB", "InfluxDB", "Neo4j",
    ],
    "devops": [
        "Docker", "Kubernetes", "AWS", "Azure", "Google Cloud", "GCP",
        "Terraform", "Ansible", "Jenkins", "GitLab CI", "GitHub Actions",
        "CI/CD", "Helm", "ArgoCD", "Prometheus", "Grafana", "ELK Stack",
        "Nginx", "Apache", "Linux", "Ubuntu", "CentOS", "Debian",
        "Vagrant", "Puppet", "Chef",
    ],
    "ml": [
        "TensorFlow", "PyTorch", "scikit-learn", "Keras", "pandas",
        "NumPy", "Matplotlib", "Seaborn", "Jupyter", "NLTK", "spaCy",
        "Hugging Face", "BERT", "GPT", "OpenCV", "YOLO", "MLflow",
        "DVC", "Apache Spark", "Hadoop", "Hive", "Airflow", "dbt",
        "XGBoost", "LightGBM",
    ],
    "tools": [
        "Git", "GitHub", "GitLab", "Bitbucket", "Jira", "Confluence",
        "Trello", "Postman", "Swagger", "SonarQube", "Maven", "Gradle",
        "npm", "Figma", "VS Code", "IntelliJ",
    ],
    "methods": [
        "Agile", "Scrum", "Kanban", "TDD", "BDD", "DevOps",
        "Microservices", "SOLID", "Design Patterns",
    ],
    "security": [
        "OWASP", "Pentesting", "Ethical hacking", "SSL/TLS", "OAuth",
        "JWT", "Keycloak", "Burp Suite", "Wireshark", "SIEM", "Splunk",
    ],
    "network": [
        "TCP/IP", "DNS", "DHCP", "VPN", "Firewall", "Cisco", "HTTP",
    ],
    "bi": [
        "Power BI", "Tableau", "QlikView", "SAP", "SQL", "Excel", "DAX",
    ],
    "soft": [
        "Leadership", "Communication", "Travail en équipe",
        "Résolution de problèmes", "Autonomie", "Rigueur",
        "Adaptabilité", "Gestion de projet", "Créativité",
        "Prise de décision", "Esprit d'analyse",
    ],
    "langs": [
        "Français", "Anglais", "Arabe", "Allemand", "Espagnol",
    ],
    "certs": [
        "AWS Certified", "Google Cloud Certified", "Azure Certified",
        "CISSP", "CCNA", "CCNP", "PMP", "ITIL", "ISO 27001",
        "Certified ScrumMaster", "Oracle Certified",
        "Certified Kubernetes Administrator",
    ],
}

ALL_SKILLS = []
for v in SKILLS.values():
    ALL_SKILLS.extend(v)
ALL_SKILLS = sorted(set(ALL_SKILLS), key=len, reverse=True)


def annotate(text: str) -> list:
    """Annote automatiquement les skills dans un texte."""
    found     = []
    text_lower = text.lower()

    for skill in ALL_SKILLS:
        sl  = skill.lower()
        pos = 0
        while True:
            idx = text_lower.find(sl, pos)
            if idx == -1:
                break
            end = idx + len(skill)

            # Frontière de mot (autoriser . et - comme terminateurs)
            before = text[idx-1] if idx > 0 else " "
            after  = text[end]   if end < len(text) else " "

            ok = (not before.isalpha()) and (not after.isalpha())
            if ok:
                overlap = any(not (end <= e[0] or idx >= e[1]) for e in found)
                if not overlap:
                    found.append((idx, end, "SKILL"))
            pos = idx + 1

    return sorted(found)


def mk(text: str) -> tuple:
    return (text, {"entities": annotate(text)})


# ─── DATASET MANUEL HAUTE QUALITÉ ────────────────────────────────────────────
# Couvre les vraies structures trouvées dans des CVs

MANUAL = []

# ── Section COMPÉTENCES — format liste séparée par virgules ──────────────────
skills_lines = [
    "Langages : Python, Java, JavaScript, TypeScript, PHP",
    "Frontend : React, Vue.js, Angular, HTML5, CSS3, Bootstrap",
    "Backend : Django, Flask, FastAPI, Node.js, Spring Boot, Laravel",
    "Bases de données : MySQL, PostgreSQL, MongoDB, Redis, Elasticsearch",
    "DevOps : Docker, Kubernetes, Jenkins, GitLab CI, GitHub Actions",
    "Cloud : AWS, Azure, Google Cloud, Terraform, Ansible",
    "IA/ML : TensorFlow, PyTorch, scikit-learn, pandas, NumPy",
    "Outils : Git, GitHub, Jira, Postman, VS Code, Figma",
    "Méthodologies : Agile, Scrum, Kanban, TDD, DevOps",
    "Sécurité : OWASP, JWT, OAuth, SSL/TLS, Keycloak",
    "Python, Django, Flask, FastAPI, Celery, Redis",
    "React, TypeScript, Redux, GraphQL, Tailwind CSS",
    "Java, Spring Boot, Hibernate, Maven, PostgreSQL",
    "Docker, Kubernetes, Helm, ArgoCD, Prometheus, Grafana",
    "AWS EC2, S3, Lambda, RDS, CloudFront, Route 53",
    "MySQL, PostgreSQL, MongoDB, SQLite, Redis, Elasticsearch",
    "TensorFlow, PyTorch, scikit-learn, Keras, OpenCV, YOLO",
    "Git, GitHub, GitLab, Jira, Confluence, Trello, Slack",
    "Flutter, React Native, iOS, Android, Swift, Kotlin",
    "PHP, Laravel, Symfony, WordPress, Drupal, MySQL",
    "Linux, Ubuntu, CentOS, Nginx, Apache, Bash, Shell",
    "Oracle, SQL Server, PL/SQL, SSIS, SSRS, Crystal Reports",
    "Apache Spark, Hadoop, Hive, Kafka, Airflow, dbt",
    "Power BI, Tableau, QlikView, SAP, Excel, DAX",
    "CCNA, TCP/IP, DNS, DHCP, VPN, Firewall, Cisco",
    "Figma, Adobe XD, HTML5, CSS3, Bootstrap, Tailwind CSS",
    "Pytest, Jest, Cypress, Selenium, JUnit, Mockito",
    "SonarQube, Maven, Gradle, npm, pip, Poetry",
    "Elasticsearch, Kibana, Logstash, Beats",
    "Terraform, Ansible, Puppet, Chef, Vagrant",
]
for line in skills_lines:
    MANUAL.append(mk(line))

# ── Section EXPÉRIENCE — phrases longues ─────────────────────────────────────
experience_lines = [
    "Développement d'une application web avec React et Django REST Framework",
    "Mise en place d'une infrastructure Docker et Kubernetes sur AWS",
    "Implémentation de tests automatisés avec Jest et Pytest",
    "Développement de microservices avec Python et FastAPI",
    "Gestion de bases de données PostgreSQL et Redis",
    "Déploiement continu avec Jenkins et GitLab CI sur Azure",
    "Création d'une API REST avec Node.js, Express.js et MongoDB",
    "Développement d'une application mobile avec Flutter et Firebase",
    "Mise en production sur AWS EC2 avec Docker et Nginx",
    "Implémentation d'un pipeline CI/CD avec GitHub Actions et Docker",
    "Analyse de données avec Python, pandas et Matplotlib",
    "Développement frontend avec Vue.js, Vuex et TypeScript",
    "Administration de bases de données Oracle et SQL Server",
    "Déploiement d'applications Spring Boot sur Kubernetes",
    "Utilisation d'Elasticsearch pour la recherche full-text",
    "Mise en place d'un monitoring avec Prometheus et Grafana",
    "Développement d'une application React Native pour iOS et Android",
    "Intégration d'OAuth2 et JWT pour la sécurisation des API",
    "Automatisation d'infrastructure avec Terraform et Ansible",
    "Développement d'un chatbot avec Python, NLTK et TensorFlow",
    "Traitement de données massives avec Apache Spark et Hadoop",
    "Mise en place d'un pipeline de données avec Apache Airflow",
    "Développement d'un modèle ML avec scikit-learn et XGBoost",
    "Utilisation de Kafka pour la messagerie asynchrone",
    "Développement d'une PWA avec Next.js et GraphQL",
    "Administration réseau avec Cisco, VPN et Firewall",
    "Sécurisation des applications avec OWASP, Burp Suite et tests de pénétration",
    "Création de tableaux de bord avec Power BI et Tableau",
    "Développement de scripts d'automatisation avec Bash et Python",
    "Gestion de projet avec Jira, Confluence et méthode Agile Scrum",
    "Développement d'une API GraphQL avec Node.js et Apollo Server",
    "Migration de bases de données MySQL vers PostgreSQL",
    "Containerisation d'applications avec Docker et Docker Compose",
    "Mise en place de tests unitaires avec JUnit et Mockito",
    "Développement d'un microservice en Go avec gRPC",
    "Utilisation de Redis pour le cache et les sessions",
    "Intégration continue avec SonarQube pour la qualité du code",
    "Développement backend avec PHP Laravel et MySQL",
    "Création d'un dashboard avec React, D3.js et REST API",
    "Déploiement serverless avec AWS Lambda et API Gateway",
]
for line in experience_lines:
    MANUAL.append(mk(line))

# ── Résumés professionnels ────────────────────────────────────────────────────
summaries = [
    "Développeur Full Stack avec 4 ans d'expérience en Python, Django et React",
    "Ingénieur DevOps certifié AWS avec expertise Docker, Kubernetes et Terraform",
    "Data Scientist spécialisé en TensorFlow, PyTorch et traitement de données Python",
    "Expert Java Spring Boot et microservices avec PostgreSQL et Kafka",
    "Développeur mobile Flutter et React Native avec publication App Store et Google Play",
    "Administrateur Linux et DevOps avec maîtrise de Jenkins, Ansible et Azure",
    "Architecte cloud AWS certifié avec expertise Kubernetes et Terraform",
    "Ingénieur données Apache Spark, Hadoop, Kafka et Python",
    "Expert sécurité OWASP, pentesting et mise en place de SOC avec Splunk",
    "Développeur PHP Laravel et Symfony avec API REST et MySQL",
    "Spécialiste BI Power BI, Tableau, SAP et SQL avancé",
    "Développeur React TypeScript avec expertise Redux, GraphQL et Jest",
    "Ingénieur réseau Cisco certifié CCNA avec VPN, Firewall et TCP/IP",
    "Expert NLP Python BERT Hugging Face et spaCy",
    "Développeur Android Kotlin et iOS Swift avec Flutter",
    "Chef de projet certifié PMP avec méthode Agile Scrum et Jira",
    "Ingénieur systèmes embarqués C++ FreeRTOS et Arduino",
    "Développeur blockchain Ethereum Solidity et Web3.js",
    "Expert Oracle DBA avec PL/SQL, RMAN et Data Guard",
    "Architecte logiciel microservices Docker Kubernetes et API Gateway",
]
for line in summaries:
    MANUAL.append(mk(line))

# ── Format CV réel — sections complètes ──────────────────────────────────────
cv_sections = [
    "Langages de programmation : Python (5 ans), Java (3 ans), JavaScript (4 ans)",
    "Frameworks web : Django, Flask, FastAPI, Spring Boot, React, Vue.js",
    "Bases de données relationnelles : MySQL, PostgreSQL, Oracle, SQL Server",
    "Bases de données NoSQL : MongoDB, Redis, Elasticsearch, Cassandra",
    "Outils DevOps : Docker, Kubernetes, Jenkins, GitLab CI, Terraform",
    "Cloud : Amazon Web Services (AWS), Microsoft Azure, Google Cloud Platform (GCP)",
    "Méthodes de développement : Agile, Scrum, TDD, CI/CD, DevOps",
    "Compétences en cybersécurité : OWASP, SSL/TLS, OAuth 2.0, JWT, PKI",
    "Bibliothèques ML : TensorFlow 2.x, PyTorch, scikit-learn, Keras, OpenCV",
    "Outils de visualisation : Power BI, Tableau, Matplotlib, Seaborn, D3.js",
    "Gestion de version : Git, GitHub, GitLab, Bitbucket",
    "IDEs et outils : VS Code, IntelliJ IDEA, PyCharm, Postman, Swagger",
    "Systèmes d'exploitation : Linux Ubuntu, CentOS, Windows Server",
    "Protocoles réseau : TCP/IP, HTTP/HTTPS, REST, gRPC, WebSocket",
    "Certifications : AWS Certified Solutions Architect, Certified Kubernetes Administrator",
    "Langues : Français (courant), Anglais (professionnel), Arabe (natif)",
    "Soft skills : Leadership, Communication, Travail en équipe, Gestion de projet",
    "Python 3.x, pandas, NumPy, Matplotlib, Jupyter Notebook, Anaconda",
    "HTML5, CSS3, JavaScript ES6+, Bootstrap 5, Tailwind CSS, Sass",
    "Android Studio, Kotlin, Swift, Xcode, React Native, Expo",
]
for line in cv_sections:
    MANUAL.append(mk(line))

# ── Certifications et formations ──────────────────────────────────────────────
certs = [
    "AWS Certified Solutions Architect — Associate (2022)",
    "Certified Kubernetes Administrator (CKA) obtenu en 2023",
    "Google Cloud Professional Data Engineer — certifié 2023",
    "Microsoft Azure Administrator (AZ-104) — certifié",
    "Certification PMP (Project Management Professional) — PMI 2022",
    "CCNA Cisco Certified Network Associate",
    "CISSP — Certified Information Systems Security Professional",
    "ITIL Foundation Certificate in IT Service Management",
    "Certified ScrumMaster (CSM) — Scrum Alliance",
    "Oracle Certified Professional Java SE 11 Developer",
]
for line in certs:
    MANUAL.append(mk(line))

# ── Exemples négatifs (bruit de CV) ──────────────────────────────────────────
negatives = [
    "Mohamed Ben Ali — Développeur Full Stack",
    "Email: mohamed.benali@gmail.com | Tél: +216 55 123 456",
    "LinkedIn: linkedin.com/in/mohamedbenali",
    "Né le 15 mars 1995 à Tunis, Tunisie",
    "Adresse: 12 Rue de la Liberté, Tunis 1001",
    "RÉSUMÉ PROFESSIONNEL",
    "EXPÉRIENCE PROFESSIONNELLE",
    "FORMATION ET DIPLÔMES",
    "COMPÉTENCES TECHNIQUES",
    "CERTIFICATIONS ET FORMATIONS",
    "LANGUES",
    "LOISIRS ET CENTRES D'INTÉRÊT",
    "Master en Génie Logiciel — INSAT Tunis (2018-2020)",
    "Licence en Informatique — FST Tunis (2015-2018)",
    "Baccalauréat Sciences de l'Informatique — 2015, mention Bien",
    "Développeur Full Stack — Wevioo, Tunis (Janvier 2021 - Présent)",
    "Ingénieur Logiciel — Ooredoo Tunisia (Mars 2020 - Décembre 2020)",
    "Stage Développeur Web — Telnet Holding, Tunis (Juillet 2019 - Septembre 2019)",
    "Références disponibles sur demande",
    "Permis de conduire catégorie B",
    "Disponible immédiatement",
    "Nationalité tunisienne",
    "Situation militaire : dégagé des obligations militaires",
    "Football, lecture, voyages, photographie",
    "Passionné par l'informatique et les nouvelles technologies",
    "Ouvert à la mobilité nationale et internationale",
    "Salaire souhaité : selon grille de l'entreprise",
    "Wevioo Tunis — Développeur Senior (2021-2023)",
    "Déclaration sur l'honneur de l'exactitude des informations",
    "Fait à Tunis, le 15 avril 2026",
]
for line in negatives:
    MANUAL.append((line, {"entities": []}))


# ─── GÉNÉRATION AUTOMATIQUE ───────────────────────────────────────────────────
def gen_auto(n=300):
    """Génère des exemples supplémentaires par templates."""
    random.seed(42)
    examples = []

    # Skills en liste simple (comme dans un CV)
    flat_skills = [s for cat in SKILLS.values() for s in cat]

    # Pattern 1 : "Technologie1, Technologie2, Technologie3"
    for _ in range(80):
        n_skills = random.randint(2, 6)
        skills   = random.sample(flat_skills, n_skills)
        text     = ", ".join(skills)
        examples.append(mk(text))

    # Pattern 2 : "Catégorie : skill1, skill2, skill3"
    prefixes = [
        "Langages : ", "Frameworks : ", "Bases de données : ",
        "Outils : ", "Technologies : ", "Cloud : ", "DevOps : ",
        "Frontend : ", "Backend : ", "Mobile : ", "Sécurité : ",
        "Compétences : ", "Environnement technique : ", "Stack : ",
        "Langages maîtrisés : ", "Outils utilisés : ",
    ]
    for _ in range(60):
        prefix   = random.choice(prefixes)
        n_skills = random.randint(3, 7)
        skills   = random.sample(flat_skills, n_skills)
        text     = prefix + ", ".join(skills)
        examples.append(mk(text))

    # Pattern 3 : phrases d'expérience
    verbs = [
        "Développement d'applications avec",
        "Mise en place d'une infrastructure",
        "Utilisation de",
        "Implémentation de solutions avec",
        "Déploiement d'applications avec",
        "Migration vers",
        "Intégration de",
        "Administration de",
        "Gestion de",
        "Automatisation avec",
    ]
    contexts = [
        " pour le backend.",
        " dans un environnement Agile.",
        " en production.",
        " sur des projets clients.",
        " dans le cadre de projets.",
        ".",
        " pour améliorer les performances.",
        " en collaboration avec l'équipe.",
    ]
    for _ in range(80):
        verb     = random.choice(verbs)
        n_skills = random.randint(2, 4)
        skills   = random.sample(flat_skills, n_skills)
        ctx      = random.choice(contexts)
        if n_skills == 2:
            text = f"{verb} {skills[0]} et {skills[1]}{ctx}"
        elif n_skills == 3:
            text = f"{verb} {skills[0]}, {skills[1]} et {skills[2]}{ctx}"
        else:
            text = f"{verb} {skills[0]}, {skills[1]}, {skills[2]} et {skills[3]}{ctx}"
        examples.append(mk(text))

    # Pattern 4 : Résumés
    roles = [
        "Développeur Full Stack", "Ingénieur DevOps", "Data Scientist",
        "Développeur Backend", "Architecte Cloud", "Développeur Mobile",
        "Ingénieur IA", "Expert Sécurité", "Analyste BI", "Développeur Frontend",
    ]
    for _ in range(50):
        role     = random.choice(roles)
        n_skills = random.randint(3, 5)
        skills   = random.sample(flat_skills, n_skills)
        years    = random.randint(1, 10)
        text     = f"{role} avec {years} ans d'expérience en {', '.join(skills[:-1])} et {skills[-1]}"
        examples.append(mk(text))

    # Pattern 5 : Négatifs supplémentaires
    more_negatives = [
        f"Développeur — Entreprise XYZ, Tunis ({random.randint(2015,2020)}-{random.randint(2021,2024)})",
        f"Licence en Informatique — Université de Tunis ({random.randint(2010,2018)})",
        "Activités : Football, Lecture, Cinéma",
        f"Téléphone : +216 {random.randint(20,99)} {random.randint(100,999)} {random.randint(100,999)}",
        "Situation de famille : Célibataire",
        "Je soussigné certifie l'exactitude des informations ci-dessus.",
    ]
    for text in more_negatives:
        examples.append((text, {"entities": []}))

    return examples


# ─── BUILD ────────────────────────────────────────────────────────────────────
def build(data, path, nlp):
    db = DocBin()
    ok = err = 0
    for text, ann in data:
        doc  = nlp.make_doc(text)
        ents = []
        for s, e, lbl in ann.get("entities", []):
            span = doc.char_span(s, e, label=lbl, alignment_mode="expand")
            if span:
                ents.append(span)
        doc.ents = filter_spans(ents)
        db.add(doc)
        ok += 1
    db.to_disk(path)
    return ok


def main():
    print("=" * 65)
    print("  Build Dataset CV — structures réelles de CV tunisiens")
    print("=" * 65)

    os.makedirs("data", exist_ok=True)
    nlp = spacy.blank("fr")

    print(f"\n✓ {len(ALL_SKILLS)} skills dans le dictionnaire")

    # Combiner
    auto_data = gen_auto()
    all_data  = MANUAL + auto_data

    # Dédupliquer
    seen   = set()
    unique = []
    for item in all_data:
        if item[0] not in seen:
            seen.add(item[0])
            unique.append(item)

    random.seed(42)
    random.shuffle(unique)

    # Stats
    with_sk  = sum(1 for _, a in unique if a["entities"])
    no_sk    = len(unique) - with_sk
    print(f"✓ Total exemples :  {len(unique)}")
    print(f"  → Avec skills :   {with_sk}")
    print(f"  → Sans skills :   {no_sk}")

    # Vérifier distribution des skills
    skill_counts = {}
    for _, ann in unique:
        for _, _, _ in ann["entities"]:
            pass  # juste compter
    total_ents = sum(len(a["entities"]) for _, a in unique)
    print(f"  → Total entités : {total_ents}")

    # Split 80/20
    split = int(len(unique) * 0.8)
    train = unique[:split]
    dev   = unique[split:]

    n_tr = build(train, "data/train.spacy", nlp)
    n_dv = build(dev,   "data/dev.spacy",   nlp)

    print(f"\n✓ train.spacy : {n_tr} docs")
    print(f"✓ dev.spacy   : {n_dv} docs")

    # Sauvegarder skills list
    with open("data/skills_list.txt", "w", encoding="utf-8") as f:
        for s in sorted(ALL_SKILLS):
            f.write(s + "\n")
    print(f"✓ skills_list.txt : {len(ALL_SKILLS)} skills")

    print("\n✓ Dataset prêt ! Lance:")
    print("  python 2_train_ner.py")


if __name__ == "__main__":
    main()
