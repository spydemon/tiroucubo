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
      <div class="button-bar">
        <button
            class="ri-bold"
            :class="{ 'is-active': isActive.bold() }"
            @click.prevent="commands.bold"
        >
        </button>
        <button
            class="ri-italic"
            :class="{ 'is-active': isActive.italic() }"
            @click.prevent="commands.italic"
        >
        </button>
        <button
            class="ri-strikethrough"
            :class="{ 'is-active': isActive.strike() }"
            @click.prevent="commands.strike"
        >
        </button>
        <button
            class="ri-underline"
            :class="{ 'is-active': isActive.underline() }"
            @click.prevent="commands.underline"
        >
        </button>
        <button
            class="ri-code-line"
            :class="{ 'is-active': isActive.code() }"
            @click.prevent="commands.code"
        >
        </button>
        <button
            class="ri-paragraph"
            :class="{ 'is-active': isActive.paragraph() }"
            @click.prevent="commands.paragraph"
        >
        </button>
        <button
            class="ri-h-1"
            :class="{ 'is-active': isActive.heading({ level: 1 }) }"
            @click.prevent="commands.heading({ level: 1 })"
        >
        </button>
        <button
            class="ri-h-2"
            :class="{ 'is-active': isActive.heading({ level: 2 }) }"
            @click.prevent="commands.heading({ level: 2 })"
        >
        </button>
        <button
            class="ri-h-3"
            :class="{ 'is-active': isActive.heading({ level: 3 }) }"
            @click.prevent="commands.heading({ level: 3 })"
        >
        </button>
        <button
            class="ri-list-check"
            :class="{ 'is-active': isActive.bullet_list() }"
            @click.prevent="commands.bullet_list"
        >
        </button>
        <button
            class="ri-list-ordered"
            :class="{ 'is-active': isActive.ordered_list() }"
            @click.prevent="commands.ordered_list"
        >
        </button>
        <button
            class="ri-chat-quote-line"
            :class="{ 'is-active': isActive.blockquote() }"
            @click.prevent="commands.blockquote"
        >
        </button>
        <button
            class="ri-code-box-line"
            :class="{ 'is-active': isActive.code_block() }"
            @click.prevent="commands.code_block"
        >
        </button>
        <button
            class="ri-separator"
            @click.prevent="commands.horizontal_rule"
        >
        </button>
        <button
            class="ri-arrow-go-back-line"
            @click.prevent="commands.undo"
        >
        </button>
        <button
            class="ri-arrow-go-forward-line"
            @click.prevent="commands.redo"
        >
        </button>
        <button
            class="ri-image-line"
            @click.prevent="commands.image"
        >
        </button>
      </div>
    </editor-menu-bar>
    <editor-content :editor="editor" />
    <input type="hidden" :name="name" :value="editor.getHTML()" />
  </div>
</template>

<script>
import { Editor, EditorContent, EditorMenuBar } from 'tiptap';
import {
  CodeBlock,
  HardBreak,
  Heading,
  HorizontalRule,
  OrderedList,
  BulletList,
  ListItem,
  TodoItem,
  TodoList,
  Bold,
  Code,
  Italic,
  Link,
  Strike,
  Underline,
  History,
} from 'tiptap-extensions'
import {Image} from './TextEditor/Image.js';
import {Blockquote, Quote, Source} from './TextEditor/Blockquote.js'

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
          new Source(),
          new Quote(),
          new Blockquote(),
          new BulletList(),
          new CodeBlock(),
          new HardBreak(),
          new Heading({ levels: [1, 2, 3] }),
          new HorizontalRule(),
          new ListItem(),
          new OrderedList(),
          new TodoItem(),
          new TodoList(),
          new Link(),
          new Bold(),
          new Code(),
          new Italic(),
          new Strike(),
          new Underline(),
          new History(),
          new Image(),
        ],
      }),
    }
  },
  beforeDestroy() {
    this.editor.destroy()
  },
}
</script>
