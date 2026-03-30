<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('impersonator_id')): ?>
<div id="impersonation-banner" style="
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 99999;
    background: linear-gradient(90deg, #dc2626, #b91c1c);
    color: white;
    padding: 10px 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    font-size: 14px;
    font-family: system-ui, sans-serif;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.2);
">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px;height:20px;flex-shrink:0;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
    </svg>
    <span>
        Anda masuk sebagai <strong><?php echo e(Auth::user()->name); ?></strong>
        &nbsp;(dari akun <?php echo e(session('impersonator_name', 'Superadmin')); ?>)
    </span>
    <a href="<?php echo e(route('impersonate.leave')); ?>"
       style="
           background: white;
           color: #dc2626;
           padding: 6px 16px;
           border-radius: 6px;
           text-decoration: none;
           font-weight: 600;
           font-size: 13px;
           white-space: nowrap;
           transition: opacity 0.2s;
       "
       onmouseover="this.style.opacity='0.9'"
       onmouseout="this.style.opacity='1'"
    >
        Kembali ke Superadmin
    </a>
</div>
<style>
    #impersonation-banner ~ * { padding-bottom: 50px; }
</style>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\Project\buku-ramadhan-2025\resources\views/components/impersonation-banner.blade.php ENDPATH**/ ?>