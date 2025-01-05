/**
 * タブ切り替え機能
 */
class TabSwitcher {
    constructor() {
        this.tabButtons = document.querySelectorAll(".tab-button");
        this.tabContents = document.querySelectorAll(".tab-content");
        this.init();
    }
    
    init() {
        this.tabButtons.forEach((button) => {
            button.addEventListener("click", () => this.switchTab(button));
        });
    }
    
    switchTab(selectedButton) {
        /* タブボタンのアクティブ状態を更新 */
        this.tabButtons.forEach((button) => {
            button.classList.remove("active");
        });
        selectedButton.classList.add("active");
        
        /* コンテンツの表示/非表示を切り替え */
        const selectedTabId = selectedButton.getAttribute("data-tab");
        this.tabContents.forEach((content) => {
            if (content.id === `${selectedTabId}-content`) {
                content.classList.remove("hidden");
                setTimeout(() => {
                    content.classList.add("active");
                }, 50);
            } else {
                content.classList.remove("active");
                content.classList.add("hidden");
            }
        });
    }
}

document.addEventListener("DOMContentLoaded", () => {
    new TabSwitcher();
});
