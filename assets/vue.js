// Vue.js initialization
import Vue from 'vue';
import TextEditor from './components/vue/TextEditor';

/**
 * TODO: the element should be set dynamically.
 * Note: the editor was deprecated… If we want to use it again, don't forget to add tiptap dependencies in package.json:
 * {
 *  […]
 *  "dependencies": {
 *      […]
 *      "tiptap": "^1.32.0",
 *      "tiptap-extensions": "^1.35.0"
 *  }
 */
Vue.component('text-editor', TextEditor);
new Vue({
    el: '.main-content'
});