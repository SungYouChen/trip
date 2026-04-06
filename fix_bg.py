path = 'resources/views/layout.blade.php'
with open(path, 'r') as f:
    text = f.read()

# Target broken CSS: url('{{ $bgUrl ?: asset('bg.jpg') }}');
# Correct CSS: url('{{ $bgUrl ?: asset('bg.jpg') }}');
target = "url('{{ $bgUrl ?: asset('bg.jpg') }}');"
correct = "url('{{ $bgUrl ?: asset('bg.jpg') }}');"

if target in text:
    print(f"Found target: {target}")
    new_text = text.replace(target, correct)
    with open(path, 'w') as f:
        f.write(new_text)
    print("Replaced successfully!")
else:
    print("Target NOT FOUND!")
    # Use a more flexible match if it was partially fixed
    import re
    new_text, count = re.subn(r"url\('\{\{ \$bgUrl \?\: asset\('bg\.jpg'\) \}\}'\);", "url('{{ $bgUrl ?: asset('bg.jpg') }}');", text)
    if count:
         with open(path, 'w') as f: f.write(new_text)
         print(f"Fixed with regex: {count} times")
    else:
         print("Regex ALSO NOT FOUND!")
