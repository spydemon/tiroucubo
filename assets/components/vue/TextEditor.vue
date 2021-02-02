<!--
  Will display a tiptap text editor, it's a kind of enhanced textarea. It should be used in a form.
  Parameters:
    * name: mandatory parameter that will define the name of the input in the form,
    * value: the default value to set in the editor.

  @TODO: this editor will never has empty content, it could be an issues if we have to ensure that the content is not empty.
  More info here: https://github.com/ueberdosis/tiptap/issues/154
-->
<template>
  <div class="text-editor">
    <editor-menu-bar
        :editor="editor"
        v-slot="{ commands, isActive, focused}"
        class="menubar"
        :class="{focused: 'is-focused'}"
    >
      <button :class="{ 'is-active': isActive.bold() }" @click="commands.bold">
        Bold
      </button>
    </editor-menu-bar>
    <editor-content :editor="editor" />
    <input type="hidden" :name="name" :value="editor.getHTML()" />
  </div>
</template>

<script>
import { Editor, EditorContent, EditorMenuBar } from 'tiptap';
import { Bold } from 'tiptap-extensions';

export default {
  components: {
    EditorMenuBar,
    EditorContent,
  },
  props: ['name', 'value'],
  data() {
    return {
      // HTML form node that will handle the content of this tiptap editor.
      input: null,
      // The tiptap editor to display.
      editor: new Editor({
        // Content that will be displayed in the editor when we initialize it.
        content: this.value,
        extensions: [
            new Bold()
        ],
      }),
    }
  },
  beforeDestroy() {
    this.editor.destroy()
  },
}
</script>
