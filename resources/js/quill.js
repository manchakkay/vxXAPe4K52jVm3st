import Quill from 'quill';

import { ColorStyle } from 'quill/formats/color';
import { BackgroundStyle } from 'quill/formats/background';
// import PlainTextClipboard from 'quill-plain-text-paste';

const Parchment = Quill.import('parchment');
const Delta = Quill.import('delta');

class ShiftEnterBlot extends Parchment.Embed {
}
ShiftEnterBlot.blotName = 'ShiftEnter';
ShiftEnterBlot.tagName = 'br';


var REDACT_Quill = {
    create(element, setup = "default") {
        let SetupCopy;
        if (setup == 'no-align') {
            SetupCopy = JSON.parse(JSON.stringify(this.QuillSetup));
            SetupCopy.modules.toolbar.splice(3, 1);
        } else {
            SetupCopy = JSON.parse(JSON.stringify(this.QuillSetup));
        }
        return new Quill(element, SetupCopy);
    },
    get_after(item, arr) {
        return arr[Math.min(arr.indexOf(item) + 1, arr.length - 1)];
    },
    get_before(item, arr) {
        return arr[Math.max(arr.indexOf(item) - 1, 0)];
    },
    QuillSetup: {
        theme: "bubble",
        placeholder:
            "Введите ваш текст. Для форматирования - выделите нужный кусок.",
        modules: {
            toolbar: [
                [{ list: "ordered" }, { list: "bullet" }],
                [{ header: [2, 3, false] }],
                ["bold", "italic", "underline", "strike", "link"],
                [{ align: '' }, { align: 'center' }, { align: 'right' }, { align: 'justify' }],
                [
                    {
                        color: [
                            "",
                            "#717982",
                            "#2994E0",
                            "#13A547",
                            "#A674E2",
                            "#A48A06",
                            "#D96D63",
                        ],
                    },
                    {
                        background: [
                            "",
                            "#F4F5F6",
                            "#BCDFF8",
                            "#B3E4C7",
                            "#E4D7FA",
                            "#E4DDB1",
                            "#F4D5D3",
                        ],
                    },
                ],
                ["clean"],
            ],
            clipboard: {
                matchVisual: false
            },
            shortheaders: true,
            softreturn: true,
            keyboard: {
                bindings: {
                    up: {
                        key: 'up',
                        handler: function (range) {
                            if (range.index <= 0) {
                                let block_w = this.quill.root.closest('.block-wrapper');
                                let block_p = block_w.previousSibling.querySelector('.ql-editor'); // add ".redact-tab" for all types of blocks
                                if (block_p)
                                    window.setTimeout(() => block_p.focus(), 0);
                            }
                            return true;
                        }
                    },
                    down: {
                        key: 'down',
                        handler: function (range) {
                            if (range.index + 1 >= this.quill.getLength()) {
                                let block_w = this.quill.root.closest('.block-wrapper');
                                let block_n = block_w.nextSibling.querySelector('.ql-editor'); // add ".redact-tab" for all types of blocks
                                if (block_n)
                                    window.setTimeout(() => block_n.focus(), 0);
                            }
                            return true;
                        }
                    },
                }
            },
        }
    },
    Module_SoftReturn: function (quill, options) {
        quill.keyboard.bindings[13].unshift({
            key: 13,
            shiftKey: true,
            handler: function (range) {
                quill.updateContents(new Delta()
                    .retain(range.index)
                    .delete(range.length)
                    .insert({ "ShiftEnter": true }),
                    'user');

                if (!quill.getLeaf(range.index + 1)[0].next) {
                    quill.updateContents(new Delta()
                        .retain(range.index + 1)
                        .delete(0)
                        .insert({ "ShiftEnter": true }),
                        'user');
                }

                quill.setSelection(range.index + 1, Quill.sources.SILENT);
                return false;
            }
        });
    },
    Module_ShortHeaders: function (quill, options) {
        console.log("SHORT!");
        const states = [undefined, 3, 2];
        quill.keyboard.addBinding({
            key: 40, // Arrow Down
            shortKey: "true",
            handler: (range, context) => quill.format("header", REDACT_Quill.get_before(context.format.header, states))
        });
        quill.keyboard.addBinding({
            key: 38, // Arrow Up
            shortKey: "true",
            handler: (range, context) => quill.format("header", REDACT_Quill.get_after(context.format.header, states))
        });
        // delete quill.keyboard.bindings["9"];
    },
};

// Оригинальные модули
Quill.register({
    'style/color': ColorStyle,
    'style/background': BackgroundStyle,
}, false);

// Кастомные модули
// Quill.register("modules/clipboard", PlainTextClipboard, true);
Quill.register('modules/shortheaders', REDACT_Quill.Module_ShortHeaders, true);
Quill.register(ShiftEnterBlot, true);
Quill.register('modules/softreturn', REDACT_Quill.Module_SoftReturn, true);

export { REDACT_Quill };