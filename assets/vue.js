// Vue.js initialization
import Vue from 'vue';
import TextEditor from './components/vue/TextEditor';

/**
 * TODO: the element should be set dynamically.
 */
Vue.component('text-editor', TextEditor);
new Vue({
    el: '.main-content'
});