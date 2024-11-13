<flux:modal
    x-data="{
        loading: false,
        errors: {},
        email: '',
        password: '',
    }"
    name="login"
    class="md:w-96"
>
    <form
        @submit.prevent="
            loading = true;
            errors = {};

            axios.post('{{ localizedRoute('login') }}', { email, password })
                .then(response => {
                    if (loginAction) {
                        loginAction();
                        loginAction = null;
                        $dispatch('modal-close');
                    } else {
                        window.location.reload();
                    }
                })
                .catch(error => {
                    errors = error.response.data.errors;
                    loading = false;
                });
        "
        class="space-y-6"
        method="post"
    >
        <div>
            <flux:heading size="lg">{{ __('Login') }}</flux:heading>
            <flux:subheading>{{ __('You can bookmark any comics with an account.') }}</flux:subheading>
        </div>
        <x-error-summary></x-error-summary>
        <flux:input label="{{ __('Email') }}" x-model="email" />
        <flux:field>
            <flux:label>{{ __('Password') }}</flux:label>
            <flux:input type="password" x-model="password" viewable autocomplete />
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
            <flux:button type="submit" variant="primary" ::disabled="loading" loadable>
                {{ __('Login') }}>
            </flux:button>
        </div>
    </form>
</flux:modal>
<flux:modal
    x-data="{
        loading: false,
        errors: {},
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    }"
    name="register"
    class="md:w-96"
>
    <form
        @submit.prevent="
            loading = true;
            errors = {};

            axios.post('{{ localizedRoute('register') }}', { name, email, password, password_confirmation })
                .then(response => {
                    if (loginAction) {
                        loginAction();
                        loginAction = null;
                        $dispatch('modal-close');
                    } else {
                        window.location.reload();
                    }
                })
                .catch(error => {
                    errors = error.response.data.errors;
                    loading = false;
                });
        "
        class="space-y-6"
        method="post"
    >
        <div>
            <flux:heading size="lg">{{ __('Register') }}</flux:heading>
            <flux:subheading>{{ __('You can bookmark any comics with an account.') }}</flux:subheading>
        </div>
        <x-error-summary></x-error-summary>
        <flux:input label="{{ __('Name') }}" x-model="name" />
        <flux:input label="{{ __('Email') }}" x-model="email" />
        <flux:input label="{{ __('Password') }}" type="password" x-model="password" viewable autocomplete />
        <flux:input label="{{ __('Confirm password') }}" type="password" x-model="password_confirmation" viewable autocomplete />
        <div class="flex">
            <flux:modal.trigger name="login">
                <flux:button type="button" variant="filled" @click="$dispatch('modal-close')">{{ __('Login') }}</flux:button>
            </flux:modal.trigger>
            <flux:spacer />
            <flux:button type="submit" variant="primary" ::disabled="loading" loadable>
                {{ __('Register') }}
            </flux:button>
        </div>
    </form>
</flux:modal>
