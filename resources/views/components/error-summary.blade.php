<div x-show="Object.keys(errors).length" class="rounded-md bg-red-50 p-4">
    <div class="text-sm text-red-700">
        <ul role="list" class="list-disc space-y-1 pl-5">
            <template x-for="messages in errors">
                <template x-for="message in messages">
                    <li x-text="message"></li>
                </template>
            </template>
        </ul>
    </div>
</div>
