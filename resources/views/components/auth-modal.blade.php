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
            <flux:label class="flex justify-between">
                {{ __('Password') }}
                <flux:modal.trigger name="forget">
                    <a @click.prevent="$dispatch('modal-close')" href="#" class="text-orange-700 dark:text-amber-500 hover:underline underline-offset-4">{{ __('Forget password') }}</a>
                </flux:modal.trigger>
            </flux:label>
            <flux:input type="password" x-model="password" viewable autocomplete />
            <flux:error name="password" />
        </flux:field>
        <div class="flex">
            <flux:modal.trigger name="register">
                <flux:button type="button" variant="subtle" inset="left" @click="$dispatch('modal-close')">{{ __("Don't have an account?") }}</flux:button>
            </flux:modal.trigger>
            <flux:spacer />
            <flux:button type="submit" variant="primary" ::disabled="loading" loadable>
                {{ __('Login') }}
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
                <flux:button type="button" variant="subtle" inset="left" @click="$dispatch('modal-close')">{{ __('Back to login') }}</flux:button>
            </flux:modal.trigger>
            <flux:spacer />
            <flux:button type="submit" variant="primary" ::disabled="loading" loadable>
                {{ __('Register') }}
            </flux:button>
        </div>
    </form>
</flux:modal>

<flux:modal
    x-data="{
        loading: false,
        errors: {},
        sent: false,
        email: '',
    }"
    name="forget"
    class="md:w-96"
>
    <form
        @submit.prevent="
            loading = true;
            errors = {};

            axios.post('{{ localizedRoute('forget') }}', { email })
                .then(response => {
                    sent = true;
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
            <flux:heading size="lg">{{ __('Reset password') }}</flux:heading>
            <flux:subheading>{{ __('We will send you an email to let you reset the password.') }}</flux:subheading>
        </div>
        <div x-cloak x-show="sent" class="border-l-4 border-yellow-400 text-sm text-yellow-700 bg-yellow-50 p-4">
            {{ __('passwords.sent') }}
        </div>
        <div x-show="! sent" class="space-y-6">
            <x-error-summary></x-error-summary>
            <flux:input label="{{ __('Email') }}" x-model="email" />
            <div class="flex">
                <flux:modal.trigger name="login">
                    <flux:button type="button" variant="subtle" inset="left" @click="$dispatch('modal-close')">{{ __('Back to login') }}</flux:button>
                </flux:modal.trigger>
                <flux:spacer />
                <flux:button type="submit" variant="primary" ::disabled="loading" loadable>
                    {{ __('Reset password') }}
                </flux:button>
            </div>
        </div>
    </form>
</flux:modal>

<flux:modal
    x-data="{
        loading: false,
        errors: {},
        token: '{{ request()->get('token') }}',
        email: '{{ request()->get('email') }}',
        password: '',
        password_confirmation: '',
    }"
    x-init="$nextTick(() => {
        if (token) {
            $dispatch('modal-show', { name: 'reset' });
        }
    });"
    :closable="false"
    name="reset"
    class="md:w-96"
>
    <form
        @submit.prevent="
            loading = true;
            errors = {};

            axios.post('{{ route('reset') }}', { token, email, password, password_confirmation })
                .then(response => {
                    window.location.href = '{{ localizedRoute('bookmarks.index') }}';
                })
                .catch(error => {
                    errors = error.response.data.errors;
                    loading = false;
                });
        "
        class="space-y-6"
        method="post"
    >
        <flux:heading size="lg">{{ __('Reset password') }}</flux:heading>
        <x-error-summary></x-error-summary>
        <flux:input label="{{ __('Password') }}" type="password" x-model="password" viewable autocomplete />
        <flux:input label="{{ __('Confirm password') }}" type="password" x-model="password_confirmation" viewable autocomplete />
        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary" ::disabled="loading" loadable>
                {{ __('Reset password') }}
            </flux:button>
        </div>
    </form>
</flux:modal>
