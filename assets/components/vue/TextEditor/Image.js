/**
 * This file is a copy of this: https://github.com/ueberdosis/tiptap/issues/573#issuecomment-577534970
 */
import { Image as TiptapImage } from "tiptap-extensions";

class Image extends TiptapImage {

    getAttrPosition(dom) {
        let positionClass = 'position-center';
        dom.classList.forEach(item => {
            if (/^position-.*/.test(item)) {
                positionClass = item;
            }
        })
        return positionClass;
    }

    getAttrReverseColorOnLightTheme(dom) {
        let isClassPresent = false;
        dom.classList.forEach(item => {
            if (/^reverse-color-on-light-theme$/.test(item)) {
                isClassPresent = true;
            }
        })
        return isClassPresent;
    }

    getClassToString(node) {
        let classes = node.attrs.position;
        if (node.attrs.reverse_color_on_light_theme) {
            classes = classes + ' reverse-color-on-light-theme';
        }
        return classes;
    }

    // The prosemirror schema object
    // Take a look at https://prosemirror.net/docs/guide/#schema for a detailed explanation
    get schema() {
        return {
            attrs: {
                alt: { default: '' },
                caption: { default: '' },
                position: { default: 'position-center' },
                reverse_color_on_light_theme: { default: false },
                src: {},
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
                        alt: dom.getElementsByTagName("img")[0].getAttribute("alt"),
                        caption: dom.getElementsByTagName("figcaption")[0].textContent,
                        position: this.getAttrPosition(dom),
                        reverse_color_on_light_theme: this.getAttrReverseColorOnLightTheme(dom),
                        src: dom.getElementsByTagName("img")[0].getAttribute("src"),
                    })
                }
            ],
            // "toDOM" will generated the output HTML of this plugin.
            toDOM: node => [
                // <figure class="{{node.attrs.position}}">
                "figure", {'class' : this.getClassToString(node)}, [
                    // <img src="{{node.attrs.src}}" />
                    "img", {'src' : node.attrs.src, 'alt' : node.attrs.alt}],
                    // <figcaption>{{node.attrs.caption}}</figcaption>
                    ['figcaption', {}, node.attrs.caption]
                // </figure>
            ],
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
                    positions_available: [
                        {value: 'position-left', label : 'Float left'},
                        {value: 'position-center', label : 'Centered'},
                        {value: 'position-right', label : 'Float right'},
                    ]
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
                },
                reverse_color_on_light_theme: {
                    get() {return this.node.attrs.reverse_color_on_light_theme;},
                    set(reverse_color_on_light_theme) {this.updateAttrs({reverse_color_on_light_theme});}
                },
                position: {
                    get() {return this.node.attrs.position;},
                    set(position) {this.updateAttrs({position});}
                }
            },
            template: `
          <figure :class="[position, {'reverse-color-on-light-theme' : reverse_color_on_light_theme}]">
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
              <div class="metadata">
                <label>Alignment</label>
                <select v-model="position">
                  <option 
                      v-for="current_position in positions_available"
                      :value="current_position.value"
                      :selected="current_position.value === position"
                  >
                    {{current_position.label}}
                  </option>
                </select>
              </div>
              <div class="metadata">
                <label>Revert color</label>
                <input
                    v-model="reverse_color_on_light_theme"
                    type="checkbox" />
              </div>
            </figcaption>
          </figure>
        `
        };
    }
}

export { Image };