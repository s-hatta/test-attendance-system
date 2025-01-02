/**
 * 入力された4桁の数値文字列を変換して表示する（1234->12:34）
 */
export class TimeInputFormatter {
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
            const timeInputs = document.querySelectorAll(".time-input");
            timeInputs.forEach((input) => {
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
        /* コロンを削除して数字だけの状態にする */
        input.value = input.value.replace(/:/g, "");
    }

    /**
     * フォーカスが外れたとき
     * @param {string} input
     */
    handleBlur(input) {
        let value = input.value;
        if (value.length < 4) {
            value = ("0000" + value).slice(-4);
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
     * 入力値が有効であるか
     * @param {string} value
     * @returns {boolean}
     */
    isValidTime(value) {
        const hours = parseInt(value.slice(0, 2));
        const minutes = parseInt(value.slice(2));
        return hours >= 0 && hours <= 23 && minutes >= 0 && minutes <= 59;
    }

    /**
     *
     * @param {string} value
     * @returns {string} - [12:34]という形式
     */
    formatDisplay(value) {
        return value.slice(0, 2) + ":" + value.slice(2);
    }
}

new TimeInputFormatter();
