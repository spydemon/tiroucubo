import {Blockquote as TiptapBlockquote} from "tiptap-extensions";
export {Blockquote};

class Blockquote extends TiptapBlockquote
{

    get schema() {
        return {
            attrs: {
                quote: {default: 'Default content'},
                source: {default: ''}
            },
            content: 'block*',
            group: 'block',
            defining: true,
            draggable: false,
            parseDOM: [{
                tag: 'blockquote',
                getAttrs: dom => ({
                    quote: dom.getElementsByTagName('p')[0]?.innerHTML,
                    source: dom.getElementsByTagName('cite')[0]?.innerHTML,
                })
            }],
            toDOM: node => [
                //<blockquote>
                'blockquote', {}, [
                    //<p>{{node.attrs.quote}}</p>
                    'p', {}, node.attrs.quote
                ], [
                    //<cite>{{node.attrs.cite}}</cite}
                    'cite', {}, node.attrs.source
                ]
            ]
        };
    }

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
            props: ["node", "updateAttrs"],
            computed: {
                quote: {
                    get() {return this.node.attrs.quote},
                    set(quote) {this.updateAttrs({quote})}
                },
                source: {
                    get() {return this.node.attrs.source},
                    set(source) {this.updateAttrs({source})}
                }
            },
            methods: {
                update(target, event) {
                    if (target === 'source') {
                        this.source = event.target?.innerHTML;
                    } else {
                        this.quote = event.target?.innerHTML;
                    }
                },
            },
            template: `
                <blockquote>
                  <p contenteditable="true" @keyup="update('quote', $event)">{{quote}}</p>
                  <div class="metadata">
                    <label>Source:</label>
                    <p contenteditable="true" @keyup="update('source', $event)">{{source}}</p>
                  </div>
                </blockquote>
            `
        }
    }
}