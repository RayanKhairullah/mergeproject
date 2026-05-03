@props([
    'placeholder' => 'Write something...',
])

@assets
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
        value: @entangle($attributes->wire('model')),
        init() {
            let checkQuill = setInterval(() => {
                if (typeof Quill !== 'undefined') {
                    clearInterval(checkQuill);
                    this.setupQuill();
                }
            }, 100);
        },
        setupQuill() {
            if (this.$refs.editor.querySelector('.ql-editor')) return;

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

            this.$watch('value', (newValue) => {
                if (newValue !== quill.root.innerHTML && typeof newValue !== 'undefined') {
                    quill.root.innerHTML = newValue || '';
                }
            });
        }
    }"
>
    <!-- Container untuk Quill -->
    <div wire:ignore class="bg-white dark:bg-zinc-900 rounded-lg min-h-[150px] border border-zinc-200 dark:border-zinc-700">
        <div x-ref="editor"></div>
    </div>
</div>
