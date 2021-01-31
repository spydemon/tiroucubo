// Vue.js initialization
import Vue from 'vue';
import TextEditor from './components/vue/TextEditor';
new Vue({
    components: { TextEditor },
    template: "<text-editor />"
}).$mount('#app');