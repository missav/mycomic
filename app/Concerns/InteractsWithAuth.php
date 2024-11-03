<?php

namespace App\Concerns;

use App\Models\User;
use App\Recombee\Recombee;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Recombee\RecommApi\Requests\MergeUsers;

trait InteractsWithAuth
{
    use WithUserUuid;

    public bool $isLoggedIn = false;

    public ?string $actionAfterLogin = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function register(): void
    {
        if (user()) {
            abort(403);
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:4', 'confirmed'],
        ];

        if (property_exists($this, 'availableActionsAfterLogin')) {
            $rules['actionAfterLogin'] = ['nullable', Rule::in($this->availableActionsAfterLogin)];
        }

        $data = $this->validate($rules);

        auth()->login(User::create([
            'id' => $this->getUserUuid() ?? Str::uuid(),
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]), true);

        $this->isLoggedIn = true;
        $this->dispatch('modal-close');
        $this->dispatch('user-uuid-updated', userUuid: user()->id);
        $this->reset('email', 'password');

        if ($this->actionAfterLogin) {
            $this->{$this->actionAfterLogin}();
        }
    }

    public function login(): void
    {
        if (user()) {
            abort(403);
        }

        $rules = [
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:4'],
        ];

        if (property_exists($this, 'availableActionsAfterLogin')) {
            $rules['actionAfterLogin'] = ['nullable', Rule::in($this->availableActionsAfterLogin)];
        }

        $data = $this->validate($rules);

        $data = Arr::only($data, ['email', 'password']);

        $originalUserUuid = $this->getUserUuid();

        if (! auth()->attempt($data, true)) {
            $this->addError('password', __('auth.failed'));
            return;
        }

        request()->session()->regenerate();

        $this->isLoggedIn = true;
        $this->dispatch('modal-close');
        $this->dispatch('user-uuid-updated', userUuid: user()->id);
        $this->reset('email', 'password');

        if ($this->actionAfterLogin) {
            $this->{$this->actionAfterLogin}();
        }

        if ($originalUserUuid && $originalUserUuid !== user()->id) {
            try {
                Recombee::send(new MergeUsers(user()->id, $originalUserUuid, [
                    'cascadeCreate' => true,
                ]));
            } catch (Exception) {}
        }
    }
}
