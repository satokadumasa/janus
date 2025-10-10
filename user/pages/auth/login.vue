<script setup lang="ts">
import { FetchError } from "ofetch";

useSeoMeta({
  title: "Login",
});

definePageMeta({
  middleware: ["$guest"],
});

const { csrf, user, isLoggedIn, login, logout, refreshUser } = useSanctum()
const router = useRouter();

const form = ref({
  username: "",
  password: "",
});

const busy = ref(false);
const errors = ref<ValidationErrors>({})

const submitForm = async () => {
  try {
    errors.value = {}
    busy.value = true
    await login(form.value)
    await refreshUser()
    return navigateTo('/')
  } catch (err) {
    busy.value = false;
    if (err instanceof FetchError && err.response?.status === 422) {
      errors.value = err.response._data.errors;
    }
  }
};
</script>

<template>
    <div class="container">
        <form @submit.prevent="submitForm">
            <div class="row">
                <div class="col-xs-4 col-sm-2 col-md-2 col-lg-1 col-xl-1 col-xxl-1">
                    <label for="username" class="text-sm font-medium">User Name</label>
                </div>
                <div class="col-xs-4 col-sm-10 col-md-10 col-lg-11 col-xl-11 col-xxl-11">
                    <input
                        id="username"
                        type="username"
                        v-model="form.username"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                    />
                </div>
            </div>
            <div class="row">
                <div class="col-xs-4 col-sm-2 col-md-2 col-lg-1 col-xl-1 col-xxl-1">
                    <label for="password" class="text-sm font-medium">Password</label>
                </div>
                <div class="col-xs-4 col-sm-10 col-md-10 col-lg-11 col-xl-11 col-xxl-11">
                    <input
                        id="password"
                        type="password"
                        v-model="form.password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                    />
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
                    <button
                        type="submit"
                        class="btn btn-primary"
                        :class="{
                            'hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300':
                            !busy,
                            'opacity-50 cursor-not-allowed': busy,
                        }"
                    >
                    Sign In
                    </button>
                </div>
            </div>
        </form>
    </div>
    <!-- <div
        class="flex flex-col items-center justify-center min-h-screen bg-gray-100"
    >
        <div class="w-full max-w-md p-8 space-y-3 bg-white shadow-lg rounded-xl">
        <h1 class="text-2xl font-bold text-center">Login</h1>
        <form @submit.prevent="submitForm">
            <div class="flex flex-col space-y-1">
            <label for="username" class="text-sm font-medium">User Name</label>
            <input
                id="username"
                type="username"
                v-model="form.username"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
            />
            <span class="text-sm text-red-600" v-if="errors.username">
                {{ errors.username[0] }}
            </span>
            </div>

            <div class="flex flex-col mt-3 space-y-1">
            <label for="password" class="text-sm font-medium">Password</label>
            <input
                id="password"
                type="password"
                v-model="form.password"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
            />
            <span class="text-sm text-red-600" v-if="errors.password">
                {{ errors.password[0] }}
            </span>
            </div>

            <button
            type="submit"
            class="btn btn-primary"
            :class="{
                'hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300':
                !busy,
                'opacity-50 cursor-not-allowed': busy,
            }"
            >
            Sign In
            </button>
        </form>
        </div>
    </div> -->
</template>
