document.addEventListener("DOMContentLoaded", () => {
  const header = document.querySelector("[data-header]");
  const navToggle = document.querySelector("[data-nav-toggle]");
  const navPanel = document.querySelector("[data-nav-panel]");
  const modal = document.querySelector("[data-form-modal]");

  const formTypes = window.mmTheme?.formTypes || {};

  function syncHeader() {
    if (!header) return;
    header.classList.toggle("is-scrolled", window.scrollY > 18);
  }

  syncHeader();
  window.addEventListener("scroll", syncHeader, { passive: true });

  if (navToggle && navPanel) {
    navToggle.addEventListener("click", () => {
      const isOpen = navPanel.classList.toggle("is-open");
      navToggle.setAttribute("aria-expanded", String(isOpen));
    });

    navPanel.querySelectorAll("a").forEach((link) => {
      link.addEventListener("click", () => {
        navPanel.classList.remove("is-open");
        navToggle.setAttribute("aria-expanded", "false");
      });
    });
  }

  document.querySelectorAll("[data-reveal]").forEach((element) => {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-visible");
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.18 }
    );

    observer.observe(element);
  });

  document.querySelectorAll("[data-carousel]").forEach((carousel) => {
    const track = carousel.querySelector("[data-carousel-track]");
    const prev = carousel.parentElement?.querySelector("[data-carousel-prev]");
    const next = carousel.parentElement?.querySelector("[data-carousel-next]");

    if (!track) return;

    const step = () => {
      const card = track.querySelector(".showcase-card");
      return card ? card.getBoundingClientRect().width + 22 : 320;
    };

    prev?.addEventListener("click", () => {
      track.scrollBy({ left: -step(), behavior: "smooth" });
    });

    next?.addEventListener("click", () => {
      track.scrollBy({ left: step(), behavior: "smooth" });
    });
  });

  document.querySelectorAll(".showcase-card").forEach((card) => {
    const visuals = card.querySelectorAll("[data-visual]");
    const buttons = card.querySelectorAll("[data-visual-trigger]");

    buttons.forEach((button) => {
      button.addEventListener("click", () => {
        const target = button.getAttribute("data-visual-trigger");
        buttons.forEach((item) => item.classList.toggle("is-active", item === button));
        visuals.forEach((visual) => {
          visual.classList.toggle("is-active", visual.getAttribute("data-visual") === target);
        });
      });
    });
  });

  document.querySelectorAll(".faq-item").forEach((item) => {
    const toggle = item.querySelector(".faq-item__toggle");
    toggle?.addEventListener("click", () => {
      const open = item.classList.toggle("is-open");
      toggle.setAttribute("aria-expanded", String(open));
      const icon = item.querySelector(".faq-item__icon");
      if (icon) icon.textContent = open ? "−" : "+";
    });
  });

  function setModalFormType(type) {
    if (!modal) return;
    modal.querySelectorAll("[data-form-type-input]").forEach((input) => {
      input.value = type;
    });
    const title = modal.querySelector("[data-form-title]");
    if (title) {
      title.textContent = formTypes[type] || formTypes.project || title.textContent;
    }
  }

  function openModal(type = "project") {
    if (!modal) return;
    setModalFormType(type);
    modal.hidden = false;
    document.body.style.overflow = "hidden";
  }

  function closeModal() {
    if (!modal) return;
    modal.hidden = true;
    document.body.style.overflow = "";
  }

  document.querySelectorAll("[data-form-trigger]").forEach((button) => {
    button.addEventListener("click", () => {
      openModal(button.getAttribute("data-form-type") || "project");
    });
  });

  modal?.addEventListener("click", (event) => {
    if (event.target === modal || event.target.hasAttribute("data-form-close")) {
      closeModal();
    }
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      closeModal();
    }
  });
});
