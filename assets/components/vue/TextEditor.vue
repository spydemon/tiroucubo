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
            class="menubar__button"
            :class="{ 'is-active': isActive.bold() }"
            @click.prevent="commands.bold"
        >
          B
          <icon name="bold" />
        </button>
        <button
            class="menubar__button"
            :class="{ 'is-active': isActive.italic() }"
            @click.prevent="commands.italic"
        >
          I
          <icon name="italic" />
        </button>
        <button
            class="menubar__button"
            :class="{ 'is-active': isActive.strike() }"
            @click.prevent="commands.strike"
        >
          S
          <icon name="strike" />
        </button>
        <button
            class="menubar__button"
            :class="{ 'is-active': isActive.underline() }"
            @click.prevent="commands.underline"
        >
          U
          <icon name="underline" />
        </button>
        <button
            class="menubar__button"
            :class="{ 'is-active': isActive.code() }"
            @click.prevent="commands.code"
        >
          C
          <icon name="code" />
        </button>
        <button
            class="menubar__button"
            :class="{ 'is-active': isActive.paragraph() }"
            @click.prevent="commands.paragraph"
        >
          P
          <icon name="paragraph" />
        </button>
        <button
            class="menubar__button"
            :class="{ 'is-active': isActive.heading({ level: 1 }) }"
            @click.prevent="commands.heading({ level: 1 })"
        >
          H1
        </button>
        <button
            class="menubar__button"
            :class="{ 'is-active': isActive.heading({ level: 2 }) }"
            @click.prevent="commands.heading({ level: 2 })"
        >
          H2
        </button>
        <button
            class="menubar__button"
            :class="{ 'is-active': isActive.heading({ level: 3 }) }"
            @click.prevent="commands.heading({ level: 3 })"
        >
          H3
        </button>
        <button
            class="menubar__button"
            :class="{ 'is-active': isActive.bullet_list() }"
            @click.prevent="commands.bullet_list"
        >
          UL
          <icon name="ul" />
        </button>
        <button
            class="menubar__button"
            :class="{ 'is-active': isActive.ordered_list() }"
            @click.prevent="commands.ordered_list"
        >
          OL
          <icon name="ol" />
        </button>
        <button
            class="menubar__button"
            :class="{ 'is-active': isActive.blockquote() }"
            @click.prevent="commands.blockquote"
        >
          Q
          <icon name="quote" />
        </button>
        <button
            class="menubar__button"
            :class="{ 'is-active': isActive.code_block() }"
            @click.prevent="commands.code_block"
        >
          C
          <icon name="code" />
        </button>
        <button
            class="menubar__button"
            @click.prevent="commands.horizontal_rule"
        >
          HR
          <icon name="hr" />
        </button>
        <button
            class="menubar__button"
            @click.prevent="commands.undo"
        >
          &lt;
          <icon name="undo" />
        </button>
        <button
            class="menubar__button"
            @click.prevent="commands.redo"
        >
          &gt;
          <icon name="redo" />
        </button>
        <button
            class="menubar__button"
            @click.prevent="showImagePrompt(commands.image)"
        >
          Img
          <icon name="image" />
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
  Blockquote,
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
  Image,
} from 'tiptap-extensions'

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
  methods: {
    //TODO: find a way to set image dimension.
    //TODO: find a way to optimize images size on mobile.
    //TODO: find a way to be able to edit the images.
    //TODO: find a nicer prompter.
    showImagePrompt(command) {
      const src = prompt('URL of the Image')
      const alt = prompt('Alternative text of the Image')
      const title = prompt('Title of the Image')
      if (src !== null) {
        command({ src, alt: alt, title: title })
      }
    },
  },
}
</script>
