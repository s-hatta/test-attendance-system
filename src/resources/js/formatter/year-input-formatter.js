/**
 * 入力された4桁の数値文字列を変換して表示する（1234->1234年）
 */
export class YearInputFormatter {
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
            const timeInputs = document.querySelectorAll(".year-input");
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
        input.value = input.value.replace("年", "");
    }

    /**
     * フォーカスが外れたとき
     * @param {string} input
     */
    handleBlur(input) {
        const value = input.value;
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
     * @returns {string} - [1234年]という形式
     */
    formatDisplay(value) {
        return value + "年";
    }
}

new YearInputFormatter();
