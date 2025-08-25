import { createRouter, createWebHashHistory } from 'vue-router'

import Events from '@/pages/Events/Home.vue'
import Event from '@/pages/Events/Event.vue'

const router = createRouter({
    history: createWebHashHistory(),
    routes: [
        { path: '/', name: 'events.index', component: Events },
        { path: '/events/:slug', name: 'events.show', component: Event, props: false },
        { path: '/:pathMatch(.*)*', redirect: '/' },
    ],
    scrollBehavior(_to, _from, saved) {
        return saved ?? { top: 0 }
    },
})

export default router
