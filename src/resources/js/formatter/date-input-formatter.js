/**
 * 入力された4桁の数値文字列を変換して表示する（0123->01月23日）
 */
export class DateInputFormatter {
    /**
     * コンストラクタ
     */
    constructor() {
        this.initialize();
    }

    /**
     * 初期化
     */
    initialize() {
        document.addEventListener("DOMContentLoaded", () => {
            const dateInputs = document.querySelectorAll(".date-input");
            dateInputs.forEach((input) => {
                this.formatInitialValue(input);
                this.attachEventListeners(input);
            });
        });
    }

    /**
     * 初期化時の表示
     * @param {string} input
     */
    formatInitialValue(input) {
        const value = input.dataset.originalValue;
        input.value = this.formatDisplay(value);
    }

    /**
     * イベントの登録
     * @param {string} input
     */
    attachEventListeners(input) {
        input.addEventListener("focus", () => this.handleFocus(input));
        input.addEventListener("blur", () => this.handleBlur(input));
        input.addEventListener("input", () => this.handleInput(input));
    }

    /**
     * フォーカスしたとき
     * @param {string} input
     */
    handleFocus(input) {
        /* 月日を削除して数字だけの状態にする */
        input.value = input.value.replace("月", "").replace("日", "");
    }

    /**
     * フォーカスが外れたとき
     * @param {string} input
     */
    handleBlur(input) {
        let value = input.value;
        if (value.length < 4) {
            value = ('0000' + value).slice(-4);
        }
        input.value = this.formatDisplay(value);
    }

    /**
     * 入力されたとき
     * @param {string} input
     */
    handleInput(input) {
        /* 数字以外の文字を削除 */
        let value = input.value.replace(/[^\d]/g, "");

        /* 4桁までに制限 */
        if (value.length > 4) {
            value = value.slice(0, 4);
        }

        input.value = value;
    }

    /**
     *
     * @param {string} value
     * @returns {string} - [01月23日]という形式
     */
    formatDisplay(value) {
        return value.slice(0, 2) + "月" + value.slice(2) + "日";
    }
}

new DateInputFormatter();
