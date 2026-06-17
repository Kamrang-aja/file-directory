document.addEventListener("DOMContentLoaded", () => {

    /* =====================================
       THEME
    ===================================== */

    const toggleBtn =
        document.getElementById("themeToggle");

    const savedTheme =
        localStorage.getItem("theme") || "light";

    document.documentElement.setAttribute(
        "data-theme",
        savedTheme
    );

    updateThemeIcon(savedTheme);

    if (toggleBtn) {

        toggleBtn.addEventListener("click", () => {

            const current =
                document.documentElement.getAttribute("data-theme");

            const next =
                current === "dark"
                    ? "light"
                    : "dark";

            document.documentElement.setAttribute(
                "data-theme",
                next
            );

            localStorage.setItem(
                "theme",
                next
            );

            updateThemeIcon(next);
        });
    }

    function updateThemeIcon(theme) {

        if (!toggleBtn) return;

        toggleBtn.innerHTML =
            theme === "dark"
                ? '<i class="bi bi-sun-fill"></i>'
                : '<i class="bi bi-moon-fill"></i>';
    }

    /* =====================================
       HEADER SHADOW ON SCROLL
    ===================================== */

    const header =
        document.querySelector(".top-header");

    function updateHeaderShadow() {

        if (!header) return;

        if (window.scrollY > 10) {

            header.classList.add("scrolled");

        } else {

            header.classList.remove("scrolled");
        }
    }

    updateHeaderShadow();

    window.addEventListener(
        "scroll",
        updateHeaderShadow
    );

    /* =====================================
       SEARCH REPOSITORY
    ===================================== */

    const searchInput =
        document.getElementById("searchInput");

    const searchBtn =
        document.getElementById("searchBtn");

    function doSearch() {

        const keyword =
            searchInput.value
                .toLowerCase()
                .trim();

        document
            .querySelectorAll("#repoTable tr")
            .forEach(row => {

                row.style.display =
                    row.textContent
                        .toLowerCase()
                        .includes(keyword)
                        ? ""
                        : "none";
            });
    }

    if (searchBtn) {

        searchBtn.addEventListener(
            "click",
            doSearch
        );
    }

    if (searchInput) {

        searchInput.addEventListener(
            "keypress",
            function (e) {

                if (e.key === "Enter") {

                    doSearch();
                }
            }
        );
    }

    /* =====================================
       HASH DETAIL MODAL
    ===================================== */

    document
        .querySelectorAll(".hash-btn")
        .forEach(btn => {

            btn.addEventListener("click", () => {

                const modalName =
                    document.getElementById("modalName");

                const modalDate =
                    document.getElementById("modalDate");

                const modalSize =
                    document.getElementById("modalSize");

                const modalHash =
                    document.getElementById("modalHash");

                if (modalName)
                    modalName.textContent =
                        btn.dataset.name || "-";

                if (modalDate)
                    modalDate.textContent =
                        btn.dataset.date || "-";

                if (modalSize)
                    modalSize.textContent =
                        btn.dataset.size || "-";

                if (modalHash)
                    modalHash.textContent =
                        btn.dataset.hash || "-";
            });

        });

});