<?php
namespace Components\Modal;

final class Modal
{
    public static function render(string $id, string $title, string $body, string $size = 'md'): string
    {
        $width = match ($size) {
            'sm' => 'max-w-md',
            'lg' => 'max-w-3xl',
            'xl' => 'max-w-5xl',
            default => 'max-w-lg',
        };

        return <<<HTML
<div id="$id" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
     x-show="modal === '$id'" x-cloak
     @click.self="modal = ''" x-transition>
    <div class="$width w-full max-h-[90vh] overflow-y-auto rounded-2xl bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
            <h3 class="text-lg font-bold text-gray-900">$title</h3>
            <button type="button" @click="modal = ''" class="text-secondary-400 hover:text-secondary-700">&times;</button>
        </div>
        <div class="p-6">$body</div>
    </div>
</div>
HTML;
    }
}
