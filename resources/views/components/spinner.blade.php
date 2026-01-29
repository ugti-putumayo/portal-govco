<div {{ $attributes->merge(['class' => 'loading-backdrop']) }} x-show="loading" x-cloak>
    <div class="loading-spinner-container">
        <div class="spinner"></div>
        <p class="loading-text">Cargando...</p>
    </div>
</div>

<style>
.loading-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 9999;
    width: 100vw;
    height: 100vh;
    background-color: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(4px);
    display: flex;
    justify-content: center;
    align-items: center;
}

.loading-spinner-container {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.spinner {
    border: 6px solid #e0e0e0;
    border-top: 6px solid #004884;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    animation: spin 1s linear infinite;
    margin-bottom: 1rem;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loading-text {
    color: #004884;
    font-size: 1.1rem;
    font-weight: bold;
}
</style>