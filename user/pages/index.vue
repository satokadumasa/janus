<script setup lang="ts">
const { csrf, user, isLoggedIn, login, logout, refreshUser } = useSanctum()
await refreshUser()

useSeoMeta({
    title: 'Home',
})

definePageMeta({
    middleware: ["$auth"],
})

</script>

<template>
    <div class="container">
        <h1 class="text-3xl font-bold">Home Page</h1>
        <div class="row">
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 d col-xl-12 col-xxl-12 d-block d-sm-none" style="height: 200px; background-color: whitesmoke; padding: 1px;">
                <div style="height: 196px; margin: 1px; padding: 5px; background-color: white;">
                    XS
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 d col-xl-12 col-xxl-12 d-none d-sm-block d-md-none" style="height: 200px; background-color: whitesmoke; padding: 1px;">
                <div style="height: 196px; margin: 1px; padding: 5px; background-color: white;">
                    SM
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 d col-xl-12 col-xxl-12 d-none d-md-block d-lg-none" style="height: 200px; background-color: whitesmoke; padding: 1px;">
                <div style="height: 196px; margin: 1px; padding: 5px; background-color: white;">
                    MD
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 d col-xl-12 col-xxl-12 d-none d-lg-block d-xl-none" style="height: 200px; background-color: whitesmoke; padding: 1px;">
                <div style="height: 196px; margin: 1px; padding: 5px; background-color: white;">
                    LG
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 d col-xl-12 col-xxl-12 d-none d-xl-block d-xxl-none" style="height: 200px; background-color: whitesmoke; padding: 1px;">
                <div style="height: 196px; margin: 1px; padding: 5px; background-color: white;">
                    XL
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 d col-xl-12 col-xxl-12 d-none d-xxl-block" style="height: 200px; background-color: whitesmoke; padding: 1px;">
                <div style="height: 196px; margin: 1px; padding: 5px; background-color: white;">
                    XXL
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4 col-xxl-4" style="height: 320px; background-color: whitesmoke; padding: 1px;">
                <div class="container">
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12" style="height: 160px; background-color: whitesmoke; padding: 1px;">
                            <div style="height: 156px; margin: 1px; padding: 5px; background-color: white;">
                                <b>運営からのお知らせ</b><br>
                                [010]<br> 
                                [010]<br> 
                                [010]<br> 
                                [010]<br> 
                                [010]<br> 
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12" style="height: 160px; background-color: whitesmoke; padding: 1px;">
                            <div style="height: 156px; margin: 1px; padding: 5px; background-color: white;">
                                <b>Message</b><br>
                                [010]<br> 
                                [010]<br> 
                                [010]<br> 
                                [010]<br> 
                                [010]<br> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4 col-xxl-4" style="height: 320px; background-color: whitesmoke; padding: 1px;">
                <div style="height: 316px; margin: 1px; padding: 5px; background-color: white;">
                    <b>フォロー新着</b><br>
                    [010]<br> 
                    [010]<br> 
                    [010]<br> 
                    [010]<br> 
                    [010]<br> 
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4 col-xxl-4" style="height: 320px; background-color: whitesmoke; padding: 1px;">
                <div style="height: 316px; margin: 1px; padding: 5px; background-color: white;">
                    <b>全体新着</b><br>
                    [010]<br> 
                    [010]<br> 
                    [010]<br> 
                    [010]<br> 
                    [010]<br> 
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4 col-xxl-4" style="height: 320px; background-color: whitesmoke; padding: 1px;">
                <div style="height: 316px; margin: 1px; padding: 5px; background-color: white;">
                    <b>全体新着</b><br>
                    [010]<br> 
                    [010]<br> 
                    [010]<br> 
                    [010]<br> 
                    [010]<br> 
                </div>
            </div>
            <div class="d-none d-sm-none d-dm-none d-lg-block col-lg-6 col-xl-4 col-xxl-4" style="height: 320px; background-color: whitesmoke; padding: 1px;">
                <div style="height: 316px; margin: 1px; padding: 5px; background-color: white;">
                    <b>全体新着</b><br>
                    [010]<br> 
                    [010]<br> 
                    [010]<br> 
                    [010]<br> 
                </div>
            </div>
            <div class="d-none d-sm-none d-dm-none d-lg-block col-lg-6 col-xl-4 col-xxl-4" style="height: 320px; background-color: whitesmoke; padding: 1px;">
                <div style="height: 316px; margin: 1px; padding: 5px; background-color: white;">
                    <b>全体新着</b><br>
                    [010]<br> 
                    [010]<br> 
                    [010]<br> 
                    [010]<br> 
                    [010]<br> 
                </div>
            </div>
        </div>
    </div>
</template>