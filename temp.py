from pathlib import Path
path = Path('resources/views/static/terms.blade.php')
text = path.read_text()
text = text.replace('organization\uFFFD?Ts', "organization's")
text = text.replace('Questions about these terms, privacy, or data handling can be sent to\n                <a href="mailto:{{  }}" class="text-indigo-700 underline">{{  }}</a>.', 'Questions about these terms, privacy, or data handling can be sent to\n                <a href="mailto:{{  }}" class="text-indigo-700 underline">{{  }}</a> or <a href="mailto:info@asylon.cc" class="text-indigo-700 underline">info@asylon.cc</a>.')
path.write_text(text)
