@props([
    'placeholder' => 'Write something...',
])

@assets
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<style>
    /* Styling agar cocok dengan Dark/Light theme Tailwind & Flux UI */
    .ql-toolbar.ql-snow {
        border-color: var(--color-zinc-200);
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
        background-color: var(--color-zinc-50);
    }
    .dark .ql-toolbar.ql-snow {
        border-color: var(--color-zinc-700);
        background-color: var(--color-zinc-800);
    }
    .ql-container.ql-snow {
        border-color: var(--color-zinc-200);
        border-bottom-left-radius: 0.5rem;
        border-bottom-right-radius: 0.5rem;
        background-color: #ffffff;
        font-family: inherit;
        font-size: 0.875rem; /* text-sm */
    }
    .dark .ql-container.ql-snow {
        border-color: var(--color-zinc-700);
        background-color: var(--color-zinc-900);
        color: #e4e4e7; /* text-zinc-200 */
    }
    .dark .ql-stroke {
        stroke: #a1a1aa !important; /* text-zinc-400 */
    }
    .dark .ql-fill {
        fill: #a1a1aa !important;
    }
    .dark .ql-picker-label {
        color: #a1a1aa;
    }
    .ql-editor {
        min-height: 150px;
    }
</style>
@endassets

<div
    class="w-full"
    x-data="{
        value: $wire.entangle('{{ $attributes->wire('model')->value() }}'),
        init() {
            setTimeout(() => {
                let quill = new Quill(this.$refs.editor, {
                    theme: 'snow',
                    placeholder: '{{ $placeholder }}',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            ['clean']
                        ]
                    }
                });

                // Set initial value
                if (this.value) {
                    quill.root.innerHTML = this.value;
                }

                quill.on('text-change', () => {
                    let html = quill.root.innerHTML;
                    if (html === '<p><br></p>') {
                        html = '';
                    }
                    this.value = html;
                });

                // Watch for changes from Livewire (e.g., when editing existing data)
                this.$watch('value', (newValue) => {
                    if (newValue !== quill.root.innerHTML && newValue !== undefined) {
                        quill.root.innerHTML = newValue || '';
                    }
                });
            }, 100);
        }
    }"
>
    <!-- Container untuk Quill -->
    <div wire:ignore>
        <div x-ref="editor"></div>
    </div>
</div>
