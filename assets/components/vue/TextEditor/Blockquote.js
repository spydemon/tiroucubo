import {Blockquote as TiptapBlockquote} from "tiptap-extensions";
import { Node, Plugin, PluginKey, Mark, Extension } from 'tiptap';
import { toggleWrap, wrappingInputRule, toggleList, toggleBlockType, setBlockType, textblockTypeInputRule, chainCommands, exitCode, nodeInputRule, splitListItem, sinkListItem, liftListItem, insertText, replaceText, splitToDefaultListItem, toggleMark, markInputRule, markPasteRule, updateMark, removeMark, pasteRule } from 'tiptap-commands';
export {Blockquote, Quote, Source};

class Blockquote extends Node
{

    init() {
        const $quote = new Quote();
        const $source = new Source();
        toggleWrap($quote.schema);
        toggleWrap($source.schema);
    }

    commands({type}) {
        return () => toggleWrap(type);
    }

    keys({
        type
    }) {
        return {
            'Ctrl->': toggleWrap(type)
        };
    }

    inputRules({
                   type
               }) {
        return [wrappingInputRule(/^\s*>\s$/, type)];
    }

    get name() {
        return 'blockquote';
    }

    get schema() {
        return {
            content: 'quote source',
            group: 'block',
            defining: true,
            draggable: false,
            parseDOM: [{
                tag: 'blockquote',
            }],
            toDOM: node => {
                return ['blockquote', 0]
            },
        };
    }

    get view() {
        return {
            template: `
                <blockquote>
                    <p contenteditable="true">Citation</p>
                    <cite contenteditable="true">Source</cite>
                </blockquote>
            `
        }
    }
}

class Quote extends Node
{
    get name() {
        return 'quote';
    }

    get schema() {
        return {
            attrs: {
                quote: {default: 'Default quote'},
            },
            content: 'block+',
            group: 'block',
            defining: true,
            draggable: false,
            parseDOM: [{
                tag: 'blockquote',
                getAttrs: dom => ({
                    quote: dom.getElementsByTagName('p')[0]?.innerHTML,
                })
            }],
            toDOM: node => ['p', {class: 'dans-quote'}, 0],
        };
    }
}

class Source extends Node {
    get name() {
        return 'source';
    }

    get schema() {
        return {
            attrs: {
                source: {default: 'Fuck off'},
            },
            content: 'block+',
            group: 'block',
            defining: true,
            draggable: false,
            parseDOM: [{
                tag: 'blockquote',
                getAttrs: dom => ({
                    quote: dom.getElementsByTagName('cite')[0]?.innerHTML,
                })
            }],
            toDOM: node => ['cite', 0],
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
            props: ["node", "updateAttrs", "view", "getPos"],
            computed: {
                source: {
                    get() {return this.node.attrs.source;},
                    set(source) {this.updateAttrs({source});}
                },
            },
            template: `
              <cite>
                  Source:
                  <input type="text" v-model="source" />
              </cite>
        `
        };
    }
}
