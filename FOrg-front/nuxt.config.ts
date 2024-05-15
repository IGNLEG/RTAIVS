import vuetify, { transformAssetUrls } from 'vite-plugin-vuetify'
export default defineNuxtConfig({
  devtools: {
    enabled: false
  },
  ssr: false,
  build: {
    transpile: ['vuetify'],
  },
  modules: [
    (_options, nuxt) => {
      nuxt.hooks.hook('vite:extendConfig', (config) => {
        // @ts-expect-error
        config.plugins.push(vuetify({ autoImport: true }))
      })
    },
    'nuxt-auth-sanctum',
    ['@nuxtjs/google-fonts', {
      families: {
        'Nunito': true,
        download: true,
        inject: true
      }
    }]
  ],
  
  vite: {
    vue: {
      template: {
        transformAssetUrls,
      },
    },
    server: {
      watch: {
        usePolling: true,
      },
    },
  },
  sanctum: {
    baseUrl: 'http://localhost',
    origin: 'http://localhost:3000',
    redirectIfAuthenticated: false,
    redirect: {
      keepRequestedRoute: false, // Keep requested route in the URL for later redirect
      onLogin: '/', // Redirect to this page after successful login
      onLogout: '/login', // Redirect to this page after successful logout
      onAuthOnly: '/login', // Redirect to this page if user is not authenticated
      onGuestOnly: '/', // Redirect to this page if user is authenticated
    },
  },
  runtimeConfig: {
    public: {
      apiURL: process.env.BACKEND_API_URL || 'http://localhost/api',
    }
  }
},)