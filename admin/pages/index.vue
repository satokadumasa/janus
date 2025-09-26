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
    <div class="flex items-center justify-center h-screen">
        <h1 class="text-3xl font-bold">Home Page</h1>
    </div>
</template>