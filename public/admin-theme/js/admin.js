
const accountRoot = document.getElementById('accountRoot');
const dropdownMenu = document.getElementById('dropdownMenu');

accountRoot.addEventListener('click', (e) => {
    e.stopPropagation(); // prevent body click
    dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
});

document.body.addEventListener('click', () => {
    dropdownMenu.style.display = 'none';
});



var style = document.createElement('style');
style.innerHTML = `
            .search-filter input::placeholder { color:#707070; font-size:14px; font-weight:400; }
            .search-filter input::-webkit-input-placeholder { color:#707070; }
            .search-filter input:-ms-input-placeholder { color:#707070; }
            .search-filter input::-ms-input-placeholder { color:#707070; }
            `;
document.head.appendChild(style);




const mobileMenu = document.querySelector('.mobile-menu');
const sidebar = document.querySelector('.admin-sidebar');
const closeBtn = document.getElementById('closeSidebar');
const breakpoint = 1070;
let isOpen = false;
let animationFrame;

// --- Dynamically create overlay if it doesn't exist ---
let overlay = document.getElementById('overlay');
if (!overlay) {
    overlay = document.createElement('div');
    overlay.id = 'overlay';
    document.body.appendChild(overlay);
}

// --- Dynamically apply CSS styles for smooth open/close ---
const applyStyles = () => {
    const styles = {
        // position: 'fixed',
        // top: '0',
        // left: '-260px',
        // width: '250px',
        // height: '100vh',
        // background: '#fff',
        // overflowY: 'auto',
        // zIndex: '1000',
    };
    Object.assign(sidebar.style, styles);

    Object.assign(overlay.style, {
        // position: 'fixed',
        // top: '0',
        // left: '0',
        // width: '100%',
        // height: '100%',
        // background: 'rgba(0,0,0,0.4)',
        // opacity: '0',
        // visibility: 'hidden',
        // zIndex: '999',
        // display: 'none'
    });
};
applyStyles();

// --- Animate sidebar and overlay ---
const animateSidebar = (open) => {
    if (window.innerWidth > breakpoint) return;

    cancelAnimationFrame(animationFrame);

    const duration = 300; // milliseconds
    const startTime = performance.now();
    const sidebarWidth = 250;
    const initialLeft = parseInt(sidebar.style.left) || (open ? -sidebarWidth : 0);
    const targetLeft = open ? 0 : -sidebarWidth;

    overlay.style.display = 'block';
    if (open) overlay.style.opacity = 0;

    const animate = (time) => {
        const elapsed = time - startTime;
        const progress = Math.min(elapsed / duration, 1);
        sidebar.style.left = initialLeft + (targetLeft - initialLeft) * progress + 'px';
        overlay.style.opacity = open ? progress * 0.4 : (1 - progress) * 0.4;

        if (progress < 1) {
            animationFrame = requestAnimationFrame(animate);
        } else {
            if (!open) overlay.style.display = 'none';
        }
    };

    animationFrame = requestAnimationFrame(animate);
};

// --- Open/Close functions ---
const openSidebar = () => {
    if (isOpen) return;
    animateSidebar(true);
    document.body.style.overflow = 'hidden';
    isOpen = true;
};

const closeSidebar = () => {
    if (!isOpen) return;
    animateSidebar(false);
    document.body.style.overflow = '';
    isOpen = false;
};

// --- Event listeners ---
mobileMenu.addEventListener('click', openSidebar);
if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
overlay.addEventListener('click', closeSidebar);
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeSidebar();
});
window.addEventListener('resize', () => {
    if (window.innerWidth > breakpoint) closeSidebar();
});















