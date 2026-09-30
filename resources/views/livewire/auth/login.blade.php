<main class="auth-page sheet-auth-page">
    <nav class="auth-nav"><a href="{{ route('login') }}"><span>✥</span> MapMaker <small>CAMPAIGN ATLAS</small></a></nav>
    <section class="auth-card sheet-auth-card">
        <span class="sheet-card-tab">SIGN IN</span>
        <div class="auth-mark">✥</div>
        <p class="eyebrow">RETURN TO YOUR CAMPAIGN</p>
        <h1>Welcome back, cartographer.</h1>
        <p class="auth-copy">Sign in to continue shaping and sharing your worlds.</p>
        <form wire:submit="authenticate" class="auth-form">
            <label><span>Email address</span><input wire:model="email" type="email" autocomplete="email" required autofocus></label>
            <label><span>Password</span><input wire:model="password" type="password" autocomplete="current-password" required></label>
            @error('email') <p class="form-error">{{ $message }}</p> @enderror
            <button class="primary-button" type="submit" wire:loading.attr="disabled">Sign in</button>
        </form>
        <p class="auth-switch">New to the atlas? <a wire:navigate href="{{ route('register') }}">Create an account</a></p>
    </section>
</main>
