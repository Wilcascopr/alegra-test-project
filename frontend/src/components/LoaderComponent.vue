<template>
  <div class="loader">
    <div class="spinner"></div>
  </div>
</template>

<style>
.loader {
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 20px 0px;
  width: 100%;
  animation: spin 2s ease infinite;
}

.spinner {
  width: 75px;
  height: 75px;
  border-radius: 50%;
  border: 8px solid #f3f3f3;
  border-top: 8px solid #3498db;
  animation: scaleInOut 2s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

@keyframes scaleInOut {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(0.5); }
}
</style>