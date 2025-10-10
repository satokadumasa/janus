<script setup lang="ts">
// const { isLoggedIn, user, logout } = useSanctum();
const { csrf, user, isLoggedIn, login, logout, refreshUser } = useSanctum()
// const { isLoggedIn } = useCurrentUser()
// await refreshUser();
// console.log("default isLoggedIn:" + isLoggedIn.value);
const logoutUser = () => logout();
</script>

<template>
    <div class="container">
        <nav class="" style="position: fixed;">
            <div class="navbar navbar-expand-lg bg-light">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-4 col-md-4 col-lg-3 col-xl-2 col-xxl-1">
                            JANUS
                        </div>
                        <div class="col-3 col-md-3 col-lg-3 col-xl-2 col-xxl-1">
                            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvas" aria-controls="offcanvas">
                                <span class="navbar-toggler-icon">AAAA</span>
                            </button>
                        </div>
                        <div class="col-5 col-md-5 col-lg-6 col-xl-8 col-xxl-10">
                            <NuxtLink to="/" class="btn btn-primary mr-4">HOME</NuxtLink>
                        </div>
                    </div>

                    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvas" aria-labelledby="offcanvasLabel">
                        <div class="offcanvas-header">
                            <h5 class="offcanvas-title"><a href="/">JANUS</a></h5>
                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body">
                            <ul class="navbar-nav">
                                <template v-if="isLoggedIn">
                                    <li class="nav-item" style="padding: 2px;">
                                        <a href="https://weed2.september-rain.com/ticket/" class="btn btn-primary">TICKET LIST</a>
                                    </li>
                                    <li class="nav-item" style="padding: 2px;">
                                        <button @click="logoutUser" class="btn btn-danger mr-4">Logout</button>
                                    </li>
                                </template>
                                <template v-if="!isLoggedIn">
                                    <li class="nav-item" style="padding: 2px;">
                                        <NuxtLink to="/auth/login" class="btn btn-primary mr-4">Login</NuxtLink>
                                    </li>                                
                                    <li class="nav-item" style="padding: 2px;">
                                        <NuxtLink to="/auth/register" class="btn btn-primary mr-4">Register</NuxtLink>
                                    </li>                                
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        <main style="padding: 56px 0px 36px;">
            <slot />
        </main>
    </div>
</template>
