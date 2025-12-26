   
        function showLoginAlert() {
            document.getElementById('loginAlertModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // منع السكرول عند فتح النافذة
        }

        function closeLoginAlert() {
            document.getElementById('loginAlertModal').classList.add('hidden');
            document.body.style.overflow = 'auto'; // إعادة السكرول
        }

        // إغلاق النافذة عند النقر في الخارج
        window.addEventListener('click', function (e) {
            const modal = document.getElementById('loginAlertModal');
            if (e.target === modal) {
                closeLoginAlert();
            }
        });



        function openSellModal(id, name) {
            document.getElementById('modalProductId').value = id;
            document.getElementById('modalProductName').innerText = "المنتج: " + name;
            document.getElementById('sellModal').classList.remove('hidden');
            updateQR();
        }

        function closeSellModal() {
            document.getElementById('sellModal').classList.add('hidden');
        }

        function updateQR() {
            const method = document.getElementById('payMethod').value;
            const qrImage = document.getElementById('qrImage');
            const qrTitle = document.getElementById('qrTitle');
            const walletNumber = document.getElementById('walletNumber');
            const qrArea = document.getElementById('qrArea');

            const myNumber = "01112215391";
            const myInstaPay = "joema11@instapay";

            if (method === "Vodafone Cash") {
                qrArea.style.display = "flex";
                qrTitle.innerText = "ادفع الآن عبر فودافون كاش";
                walletNumber.innerText = myNumber;
                qrImage.src = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=SMSTO:${myNumber}:PUSH_PAYMENT`;
            } else if (method === "InstaPay") {
                qrArea.style.display = "flex";
                qrTitle.innerText = "ادفع الآن عبر InstaPay";
                walletNumber.innerText = myInstaPay;
                qrImage.src = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=payto:${myInstaPay}`;
            } else {
                qrArea.style.display = "none";
            }
        }
        const themeToggle = document.getElementById('theme-toggle');
        const html = document.documentElement;

        // 1. فحص الحالة المحفوظة عند تحميل الصفحة
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark');
        } else {
            html.classList.remove('dark');
        }

        // 2. وظيفة الزر عند الضغط
        themeToggle.addEventListener('click', () => {
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        });

        // 3. كود القائمة المنبثقة (Hamburger Menu)
        const menuBtn = document.getElementById('menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');

        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // كود Three.js للخلفية التفاعلية في الفوتر
        const container = document.getElementById('three-canvas-container');
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, container.offsetWidth / container.offsetHeight, 0.1, 1000);

        // جعل الخلفية شفافة تماماً ليعمل Dark/Light Mode الخاص بـ Tailwind
        const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
        renderer.setSize(container.offsetWidth, container.offsetHeight);
        renderer.setPixelRatio(window.devicePixelRatio);
        container.appendChild(renderer.domElement);

        // إضافة إضاءة محايدة تناسب الوضعين
        const ambientLight = new THREE.AmbientLight(0xffffff, 1);
        scene.add(ambientLight);

        const pointLight = new THREE.PointLight(0x3b82f6, 1); // إضاءة زرقاء خفيفة
        pointLight.position.set(20, 20, 20);
        scene.add(pointLight);

        const shapes = [];
        const geometries = [
            new THREE.IcosahedronGeometry(12, 0),
            new THREE.TorusGeometry(10, 2, 16, 100),
            new THREE.OctahedronGeometry(8, 0)
        ];

        for (let i = 0; i < 15; i++) {
            // نستخدم Wireframe بلون أزرق شفاف جداً ليناسب الأبيض والأسود
            const material = new THREE.MeshPhongMaterial({
                color: 0x3b82f6,
                wireframe: true,
                transparent: true,
                opacity: 0.15 // شفافية عالية لعدم التشويش على النصوص
            });

            const shape = new THREE.Mesh(geometries[Math.floor(Math.random() * geometries.length)], material);

            shape.position.set(
                (Math.random() - 0.5) * 200,
                (Math.random() - 0.5) * 100,
                (Math.random() - 0.5) * 50
            );

            shape.rotation.set(Math.random() * Math.PI, Math.random() * Math.PI, 0);
            shape.userData.rotSpeed = {
                x: (Math.random() - 0.5) * 0.005,
                y: (Math.random() - 0.5) * 0.005
            };

            scene.add(shape);
            shapes.push(shape);
        }

        camera.position.z = 80;

        let mouseX = 0, mouseY = 0;
        document.addEventListener('mousemove', (e) => {
            mouseX = (e.clientX - window.innerWidth / 2) * 0.01;
            mouseY = (e.clientY - window.innerHeight / 2) * 0.01;
        });

        function animate() {
            requestAnimationFrame(animate);
            shapes.forEach(shape => {
                shape.rotation.x += shape.userData.rotSpeed.x;
                shape.rotation.y += shape.userData.rotSpeed.y;
                shape.position.y += Math.sin(Date.now() * 0.001 + shape.position.x) * 0.01;
            });
            camera.position.x += (mouseX - camera.position.x) * 0.05;
            camera.position.y += (-mouseY - camera.position.y) * 0.05;
            camera.lookAt(scene.position);
            renderer.render(scene, camera);
        }

        window.addEventListener('resize', () => {
            camera.aspect = container.offsetWidth / container.offsetHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(container.offsetWidth, container.offsetHeight);
        });

        animate();



        function openConsultationModal() {
            document.getElementById('consultationModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeConsultationModal() {
            document.getElementById('consultationModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            // إعادة تعيين الفورم عند الإغلاق
            setTimeout(() => {
                document.getElementById('consultationForm').classList.remove('hidden');
                document.getElementById('successMessage').classList.add('hidden');
                document.getElementById('consultationForm').reset();
            }, 500);
        }

        // معالجة إرسال الفورم باستخدام AJAX لعدم تحديث الصفحة
        document.getElementById('consultationForm').onsubmit = function (e) {
            e.preventDefault();
            const btn = document.getElementById('submitBtn');
            const loader = document.getElementById('loader');

            btn.disabled = true;
            loader.classList.remove('hidden');

            const formData = new FormData(this);

            fetch('send_email.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.text())
                .then(data => {
                    document.getElementById('consultationForm').classList.add('hidden');
                    document.getElementById('successMessage').classList.remove('hidden');
                })
                .catch(error => {
                    alert('عذراً، حدث خطأ ما. يرجى المحاولة مرة أخرى.');
                    btn.disabled = false;
                    loader.classList.add('hidden');
                });
        };
 

        