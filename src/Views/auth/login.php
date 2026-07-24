<!-- src/Views/auth/login.php -->
<form method="POST" action="<?= BASE_URL ?>/login" class="space-y-5">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <div>
        <label class="mb-1 block text-xs font-semibold text-gray-600">Email</label>
        <input type="email" name="email" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary-100" placeholder="you@example.com">
    </div>
    <div>
        <label class="mb-1 block text-xs font-semibold text-gray-600">Password</label>
        <input type="password" name="password" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary-100" placeholder="Enter password">
    </div>
    <button type="submit" class="w-full rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-800 transition">Sign In</button>
    <p class="text-center text-xs text-gray-500">
        No account? <a href="<?= BASE_URL ?>/register" class="text-primary-700 hover:underline">Create one</a>
    </p>
</form>
