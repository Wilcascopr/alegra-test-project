import { user, logOut } from '@/services/api/auth.js';

export default {
    fetchUser({ commit }) {
        return user()
            .then(response => {
                commit('setUser', {
                    data: response.user,
                    loggedIn: true,
                });
            })
            .catch(() => {
                commit('setUser', {
                    data: null,
                    loggedIn: false,
                });
            })
    },
    signOut({ commit }) {
        return logOut()
            .then(response => {
                commit('setUser', {
                    data: null,
                    loggedIn: false,
                });
            })
            .catch(() => {
            })
    }
}