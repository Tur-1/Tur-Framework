<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container">
        <a class="navbar-brand" href="<?= route('homePage') ?>"><?= config('app.name'); ?> </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <li class="nav-item">
                    <a href="<?= route('homePage') ?>" class="nav-link <?= request()->is(route('homePage')) ? 'active ' : '' ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a href="<?= route('aboutPage') ?>" class="nav-link <?= request()->is(route('aboutPage')) ? 'active ' : '' ?>">About</a>
                </li>
                <li class="nav-item">
                    <a href="<?= route('users.list') ?>" class="nav-link <?= request()->is(route('users.list') . '*') ? 'active ' : '' ?>">Users</a>
                </li>
                <li class="nav-item">
                    <a href="<?= route('admin.dashboard') ?>" class="nav-link">
                        Dashboard
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav">
                <?php if (auth()->check()) : ?>
                    <li class="nav-item dropdown">
                        <a role="button" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= auth()->user()->name ?>
                        </a>
                        <ul class="dropdown-menu ">
                            <li class="nav-item">
                                <a class="dropdown-item" href="<?= route('user.profile') ?>">Profile</a>
                            </li>

                            <li class="nav-item">
                                <form action="<?= route('logout') ?>" method="post">
                                    <?= csrf_field() ?>
                                    <button class="dropdown-item" type="submit">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                <?php endif ?>
                <?php if (auth()->guest()) : ?>
                    <li class="nav-item">
                        <a href="<?= route('login') ?>" class="nav-link <?= request()->is(route('login') . '*') ? 'active ' : '' ?>"> Log in</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= route('register') ?>" class=" nav-link <?= request()->is(route('register') . '*') ? 'active ' : '' ?>">Register</a>
                    </li>
                <?php endif ?>
            </ul>

        </div>
    </div>
</nav>