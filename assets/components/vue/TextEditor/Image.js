/**
 * This file is a copy of this: https://github.com/ueberdosis/tiptap/issues/573#issuecomment-577534970
 */
import { Image as TiptapImage } from "tiptap-extensions";

class Image extends TiptapImage {

    // The prosemirror schema object
    // Take a look at https://prosemirror.net/docs/guide/#schema for a detailed explanation
    get schema() {
        return {
            attrs: {
                src: {},
                alt: {},
                caption: {}
            },
            group: "block",
            draggable: true,
            selectable: false,
            // define how the editor will detect your node from pasted HTML
            // every blockquote tag will be converted to this blockquote node
            parseDOM: [
                {
                    // The "tag" parameter set the trigger that will activate the parsing of the DOM element by the current plugin.
                    tag: "figure",
                    // "getAttrs" will hydrate parameters of this javascript object by content fetched in the DOM from the element "locked" by the "tag."
                    getAttrs: dom => ({
                        src: dom.getElementsByTagName("img")[0].getAttribute("src") || '',
                        alt: dom.getElementsByTagName("img")[0].getAttribute("alt") || '',
                        caption: dom.getElementsByTagName("figcaption")[0].textContent || ''
                    })
                }
            ],
            // "toDOM" will generated the output HTML of this plugin.
            toDOM: node => [
                // <figure>
                "figure", {}, [
                    // <img src="{{node.attrs.src}}" />
                    "img", {'src' : node.attrs.src, 'alt' : node.attrs.alt}],
                    // <figcaption>{{node.attrs.caption}}</figcaption>
                    ['figcaption', {}, node.attrs.caption]
                // </figure>
            ]
        };
    }

    // this command will be called from menus to add a blockquote
    // `type` is the prosemirror schema object for this blockquote
    // `schema` is a collection of all registered nodes and marks
    commands({ type, schema }) {
        return attrs => (state, dispatch) => {
            return dispatch(state.tr.replaceSelectionWith(type.create(attrs)));
        };
    }

    // return a vue component
    // this can be an object or an imported component
    get view() {
        return {
            // there are some props available
            // `node` is a Prosemirror Node Object
            // `updateAttrs` is a function to update attributes defined in `schema`
            // `view` is the ProseMirror view instance
            // `options` is an array of your extension options
            // `selected` is a boolean which is true when selected
            // `editor` is a reference to the TipTap editor instance
            // `getPos` is a function to retrieve the start position of the node
            // `decorations` is an array of decorations around the node
            props: ["node", "updateAttrs", "view", "getPos"],
            data() {
                return {
                    editor: null,
                };
            },
            watch: {
                "view.editable"() {
                    this.editor.setOptions({
                        editable: this.view.editable
                    });
                }
            },
            computed: {
                alt: {
                    get() {return this.node.attrs.alt;},
                    set(alt) {this.updateAttrs({alt});}
                },
                src: {
                    get() {return this.node.attrs.src;},
                    set(src) {this.updateAttrs({src});}
                },
                caption: {
                    get() {return this.node.attrs.caption;},
                    set(caption) {this.updateAttrs({caption});}
                }
            },
            template: `
          <figure>
            <img :src="src" />
            <figcaption>
              <div class="metadata">
                  <label>Caption</label>
                  <input v-model="caption" type="text"/>
              </div>
              <div class="metadata">
                  <label>Alt</label>
                  <input v-model="alt" type="text" />
              </div>
            </figcaption>
          </figure>
        `
        };
    }
}

export { Image };