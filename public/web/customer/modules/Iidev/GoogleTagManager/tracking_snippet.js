document.addEventListener("DOMContentLoaded", () => {
  // helpers
  function processEvent(data) {
    if (!Array.isArray(window.dataLayer)) return;

    if (data.length) {
      data.forEach((element) => {
        dataLayer.push(element);
      });
    } else {
      dataLayer.push(data);
    }
  }

  // page events

  if (window.gtmLayer !== undefined) {
    processEvent(window.gtmLayer);
  }

  const telLinks = document.querySelectorAll("a[href^='tel:']");
  if (telLinks.length) {
    telLinks.forEach((tel) => {
      tel.addEventListener("click", () => {
        processEvent({ event: "call" });
      });
    });
  }

  // trigger events

  xcart.bind("gtmAddedToCart", (_, data) => {
    processEvent(data);
  });

  xcart.bind("gtmRemovedFromCart", (_, data) => {
    processEvent(data);
  });

  xcart.bind("gtmAddedToWishlist", (_, data) => {
    processEvent(data);
  });

  xcart.bind("gtmFreeGift", (_, data) => {
    processEvent(data);
  });

  xcart.bind("gtmCouponApplied", (_, data) => {
    processEvent(data);
  });

  xcart.bind("gtmProfile", (_, data) => {
    processEvent(data);
  });
});
