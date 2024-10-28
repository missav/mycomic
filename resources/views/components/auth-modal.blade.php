<flux:modal name="login" class="md:w-96 space-y-6">
    <div>
        <flux:heading size="lg">{{ __('Login') }}</flux:heading>
        <flux:subheading>{{ __('You can bookmark any comics with an account.') }}</flux:subheading>
    </div>
    <flux:input label="{{ __('Email') }}" />
    <flux:field>
        <flux:label>{{ __('Password') }}</flux:label>
        <flux:input type="password" viewable />
        <flux:error name="password" />
        <flux:description>
            <a href="#" class="text-amber-500 hover:underline underline-offset-4">{{ __('Forget password') }}</a>
        </flux:description>
    </flux:field>
    <div class="flex">
        <flux:modal.trigger name="register">
            <flux:button type="button" variant="filled" @click="$dispatch('modal-close')">{{ __('Register') }}</flux:button>
        </flux:modal.trigger>
        <flux:spacer />
        <flux:button type="submit" variant="primary">{{ __('Login') }}</flux:button>
    </div>
</flux:modal>
<flux:modal name="register" class="md:w-96">
    <form wire:submit.prevent="register" class="space-y-6">
        <div>
            <flux:heading size="lg">{{ __('Register') }}</flux:heading>
            <flux:subheading>{{ __('You can bookmark any comics with an account.') }}</flux:subheading>
        </div>
        <flux:input label="{{ __('Name') }}" wire:model="name" />
        <flux:input label="{{ __('Email') }}" wire:model="email" />
        <flux:input label="{{ __('Password') }}" type="password" wire:model="password" viewable />
        <flux:input label="{{ __('Confirm password') }}" type="password" wire:model="password_confirmation" viewable />
        <div class="flex">
            <flux:modal.trigger name="login">
                <flux:button type="button" variant="filled" @click="$dispatch('modal-close')">{{ __('Login') }}</flux:button>
            </flux:modal.trigger>
            <flux:spacer />
            <flux:button type="submit" variant="primary">{{ __('Register') }}</flux:button>
        </div>
    </form>
</flux:modal>
