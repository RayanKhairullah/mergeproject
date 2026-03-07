import re
from pathlib import Path

migrations_dir = Path('database/migrations')
files = sorted(migrations_dir.glob('*.php'))
index_pattern = re.compile(r"->(index|unique|foreignId|constrained|primary)\(")

print('Migration file -> has index-like method')
for f in files:
    text = f.read_text(encoding='utf-8')
    has_index = bool(index_pattern.search(text))
    print(f"{f.name}: {'OK' if has_index else 'NO'}")
