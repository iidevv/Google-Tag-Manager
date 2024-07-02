// helpers

function processEvent(data) {
  if (!Array.isArray(window.dataLayer)) return;

  console.log(data);
  dataLayer.push(data);
}

// page events

document.addEventListener("DOMContentLoaded", () => {
  if (dataLayer[0]?.event !== null) return;

  processEvent(dataLayer[0]);
});

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

