<!-- src/Views/auth/register.php -->
<form method="POST" action="<?= BASE_URL ?>/register" class="space-y-5">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <div>
        <label class="mb-1 block text-xs font-semibold text-gray-600">Full Name</label>
        <input type="text" name="name" required class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary-100" placeholder="John Doe">
    </div>
    <div>
        <label class="mb-1 block text-xs font-semibold text-gray-600">Email</label>
        <input type="email" name="email" required class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary-100" placeholder="you@example.com">
    </div>
    <div>
        <label class="mb-1 block text-xs font-semibold text-gray-600">Password</label>
        <input type="password" name="password" required class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary-100" placeholder="Create a password">
    </div>
    <button type="submit" class="w-full rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-800 transition">Create Account</button>
    <p class="text-center text-xs text-gray-500">
        Already have an account? <a href="<?= BASE_URL ?>/login" class="text-primary-700 hover:underline">Sign In</a>
    </p>
</form>
