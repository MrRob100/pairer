/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('order', require('./components/Order.vue').default);
Vue.component('pair', require('./components/Pair.vue').default);
Vue.component('pair-page', require('./components/PairPage.vue').default);
Vue.component('graph', require('./components/Graph.vue').default);
Vue.component('controls', require('./components/Controls.vue').default);
Vue.component('limits', require('./components/Limits.vue').default);
Vue.component('public-page', require('./components/PublicPage.vue').default);
Vue.component('list', require('./components/List.vue').default);
Vue.component('record', require('./components/Record.vue').default);
Vue.component('box', require('./components/Box.vue').default);
Vue.component('pair-record', require('./components/PairRecord.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
