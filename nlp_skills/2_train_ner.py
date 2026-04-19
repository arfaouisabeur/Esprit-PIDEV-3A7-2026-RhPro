"""
2_train_ner.py — Entraînement optimisé pour CVs complets
=========================================================
Améliorations vs v1 :
- 600 exemples (vs 80)
- 60 itérations (vs 30)
- Batch adaptatif
- Sauvegarde du meilleur modèle par F1

Usage:
    python 2_train_ner.py
"""

import spacy
import random
import os
import json
from pathlib import Path
from spacy.util import minibatch, compounding
from spacy.training import Example
from spacy.tokens import DocBin

CONFIG = {
    "n_iter":     60,
    "dropout":    0.2,
    "learn_rate": 0.001,
    "model_out":  "models/skill_ner",
    "base_model": "fr_core_news_sm",
}


def load_docs(path, nlp):
    db = DocBin().from_disk(path)
    return list(db.get_docs(nlp.vocab))


def train():
    print("=" * 65)
    print("  Entraînement NER — 60 itérations sur dataset CV complets")
    print("=" * 65)

    os.makedirs("models", exist_ok=True)

    if not Path("data/train.spacy").exists():
        print("✗ Lance d'abord: python 1_build_dataset.py")
        return

    # ── Modèle de base ────────────────────────────────────────────────────────
    try:
        nlp = spacy.load(CONFIG["base_model"])
        print(f"✓ Modèle '{CONFIG['base_model']}' chargé")
    except OSError:
        print(f"⚠ '{CONFIG['base_model']}' manquant → modèle vide français")
        print("  RECOMMANDÉ: python -m spacy download fr_core_news_sm")
        nlp = spacy.blank("fr")

    # NER
    if "ner" not in nlp.pipe_names:
        ner = nlp.add_pipe("ner", last=True)
    else:
        ner = nlp.get_pipe("ner")
    ner.add_label("SKILL")

    # Données
    train_docs = load_docs("data/train.spacy", nlp)
    dev_docs   = load_docs("data/dev.spacy",   nlp) if Path("data/dev.spacy").exists() else []
    train_ex   = [Example(nlp.make_doc(d.text), d) for d in train_docs]
    dev_ex     = [Example(nlp.make_doc(d.text), d) for d in dev_docs]

    print(f"✓ Train: {len(train_ex)} | Dev: {len(dev_ex)}\n")

    # ── Entraînement ──────────────────────────────────────────────────────────
    other_pipes = [p for p in nlp.pipe_names if p != "ner"]
    best_f1     = 0.0
    history     = []
    no_improve  = 0

    with nlp.disable_pipes(*other_pipes):
        optimizer = nlp.begin_training()
        optimizer.learn_rate = CONFIG["learn_rate"]

        for i in range(CONFIG["n_iter"]):
            random.shuffle(train_ex)
            losses  = {}
            batches = minibatch(train_ex, size=compounding(8.0, 32.0, 1.001))

            for batch in batches:
                nlp.update(batch, drop=CONFIG["dropout"], losses=losses, sgd=optimizer)

            loss = losses.get("ner", 0)
            f1 = prec = rec = 0.0

            if dev_ex:
                sc   = nlp.evaluate(dev_ex)
                f1   = sc.get("ents_f", 0)
                prec = sc.get("ents_p", 0)
                rec  = sc.get("ents_r", 0)

            history.append({"iter": i+1, "loss": round(float(loss), 2), "f1": round(float(f1), 3)})

            if (i+1) % 5 == 0 or i == 0:
                bar = "█" * int(f1 * 25) + "░" * (25 - int(f1 * 25))
                print(f"  [{i+1:3d}/{CONFIG['n_iter']}] loss={loss:8.1f}  "
                      f"F1={f1:.3f}  P={prec:.3f}  R={rec:.3f}  [{bar}]")

            if f1 > best_f1:
                best_f1    = f1
                no_improve = 0
                nlp.to_disk(CONFIG["model_out"] + "_best")
                if (i+1) % 5 != 0:
                    print(f"  ★ Iter {i+1}: meilleur F1 = {f1:.3f}")
            else:
                no_improve += 1

            # Early stopping si pas d'amélioration sur 15 itérations
            if no_improve >= 15 and i > 20:
                print(f"\n  → Early stopping à l'itération {i+1} (pas d'amélioration sur 15 itérations)")
                break

    nlp.to_disk(CONFIG["model_out"])

    # Convertir float32 → float Python natif avant JSON
    class FloatEncoder(json.JSONEncoder):
        def default(self, obj):
            try:
                return float(obj)
            except (TypeError, ValueError):
                return super().default(obj)

    with open("models/training_history.json", "w") as f:
        json.dump(history, f, indent=2, cls=FloatEncoder)

    # ── Résultat ──────────────────────────────────────────────────────────────
    print(f"\n{'='*65}")
    if best_f1 >= 0.80:
        status = "★★★ Excellent — prêt pour la production"
    elif best_f1 >= 0.65:
        status = "★★  Bon — peut être amélioré"
    elif best_f1 >= 0.50:
        status = "★   Correct — ajouter des exemples"
    else:
        status = "⚠   Insuffisant — augmenter le dataset"

    print(f"  Meilleur F1 : {best_f1:.3f}  |  {status}")
    print(f"  Modèle      : models/skill_ner_best/")

    # ── Test sur structures CV réelles ────────────────────────────────────────
    best = spacy.load(CONFIG["model_out"] + "_best")
    print(f"\n  Tests sur structures CV réelles:")
    tests = [
        "Langages : Python, Java, JavaScript, TypeScript, PHP",
        "Développement d'une API REST avec Node.js, Express.js et MongoDB",
        "Docker, Kubernetes, AWS, Terraform, Ansible",
        "Développeur Python Django avec React et PostgreSQL, 4 ans d'expérience",
        "Master en Génie Logiciel — INSAT Tunis (2020)",
        "Email: contact@gmail.com | Tel: +216 55 123 456",
        "Frontend : React, Vue.js, Angular, HTML5, CSS3",
        "Certifié AWS Solutions Architect et Kubernetes Administrator",
        "TensorFlow, PyTorch, scikit-learn, pandas, NumPy, Matplotlib",
        "Mise en place CI/CD avec Jenkins et GitLab CI sur Azure",
    ]
    for t in tests:
        doc    = best(t)
        skills = [e.text for e in doc.ents if e.label_ == "SKILL"]
        icon   = "✓" if skills else "○"
        print(f"  {icon} \"{t[:55]}\"")
        if skills:
            print(f"    → {skills}")
    print(f"\n  Étape suivante: python 3_evaluate.py")
    print("=" * 65)


if __name__ == "__main__":
    train()
