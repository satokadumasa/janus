export default defineNuxtPlugin(() => {
  const config = useRuntimeConfig();

  useFetch(`${config.public.sanctum.url}/sanctum/csrf-cookie`, {
    credentials: 'include', // ← これ必須
  })
})
