<script setup lang="ts">
const { csrf, user, isLoggedIn, login, logout, refreshUser } = useSanctum()

await refreshUser()

useSeoMeta({
    title: 'Dashboard',
})

definePageMeta({
    middleware: ['$auth'],
})

</script>

<template>
    <div class="p-6">
        <h1 class="text-xl font-bold">ダッシュボード</h1>
        <p>ようこそ、{{ user?.username }} さん</p>
    </div>
</template>
