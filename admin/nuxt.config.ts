// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },

  css: ["~/assets/css/main.css"],

  postcss: {
    plugins: {
      // tailwindcss: {},
      autoprefixer: {},
    },
  },

  modules: ["@qirolab/nuxt-sanctum-authentication"],
  runtimeConfig: {
    // sessionCookieName: 'laravel_partner_session',
    sessionExpires: 604800,
    public: {
      sanctum: {
        url: process.env.NUXT_SANCTUM_API_URL,
        csrfEndpoint: '/sanctum/csrf-cookie',
      }
    }
  },
  app: {
    head: {
      title: 'Nuxt Sanctum Auth',
      meta: [{ name: 'referrer', content: 'no-referrer' }]
    }
  },

  laravelSanctum: {
    apiUrl: process.env.NUXT_SANCTUM_API_URL,
    authMode: process.env.NUXT_AUTH_MODE,
    userResponseWrapperKey: "data",
    csrfCookieRoute: '/sanctum/csrf-cookie',
    credentials: true,
    token: {
      storageKey: "AUTH_TOKEN",
      provider: "cookie",
      responseKey: "token",
    },
    sanctumEndpoints: {
      // Endpoint to request a new CSRF token from the server
      csrf: "/sanctum/csrf-cookie",

      // Endpoint used for user authentication
      login: "/api/partner_login",

      // Endpoint used to log out users
      logout: "/api/partner_logout",

      // Endpoint to retrieve the currently authenticated user's data
      user: "/api/partner",
    },

    redirect: {
      // Preserve the originally requested route, redirecting users there after login
      enableIntendedRedirect: false,

      // Path to redirect users when a page requires authentication
      loginPath: "/auth/login",

      // URL to redirect users to when guest-only access is required
      guestOnlyRedirect: "/dashboard",

      // URL to redirect to after a successful login
      redirectToAfterLogin: "/",

      // URL to redirect to after logging out
      redirectToAfterLogout: "/auth/login",
    },
  },
})
