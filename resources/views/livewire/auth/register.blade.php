<main class="auth-page sheet-auth-page">
    <nav class="auth-nav"><a href="{{ route('login') }}"><span>✥</span> MapMaker <small>CAMPAIGN ATLAS</small></a></nav>
    <section class="auth-card sheet-auth-card">
        <span class="sheet-card-tab">CREATE ACCOUNT</span>
        <div class="auth-mark">✥</div>
        <p class="eyebrow">THE ATLAS FORGE</p>
        <h1>Begin your atlas.</h1>
        <p class="auth-copy">Create an account to save maps and invite your party.</p>
        <form wire:submit="createAccount" class="auth-form">
            <label><span>Name</span><input wire:model="name" type="text" autocomplete="name" required autofocus></label>
            <label><span>Email address</span><input wire:model="email" type="email" autocomplete="email" required></label>
            <label><span>Password</span><input wire:model="password" type="password" autocomplete="new-password" required></label>
            <label><span>Confirm password</span><input wire:model="password_confirmation" type="password" autocomplete="new-password" required></label>
            @foreach ($errors->all() as $error) <p class="form-error">{{ $error }}</p> @endforeach
            <button class="primary-button" type="submit" wire:loading.attr="disabled">Create account</button>
        </form>
        <p class="auth-switch">Already a cartographer? <a wire:navigate href="{{ route('login') }}">Sign in</a></p>
    </section>
</main>
