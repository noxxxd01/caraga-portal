/**
 * Leaflet GIS map: markers, heatmap layer, province circles.
 */

        function initializeGISMap() {
            const caragaSouthWest = L.latLng(7.9000, 125.0000); 
            const caragaNorthEast = L.latLng(10.6000, 126.8000);
            const caragaBounds = L.latLngBounds(caragaSouthWest, caragaNorthEast);

            map = L.map('gis-map', {
                zoomControl: false,             
                scrollWheelZoom: false,         
                doubleClickZoom: false,         
                touchZoom: false,               
                boxZoom: false,                 
                keyboard: false,                
                maxZoom: 13,
                minZoom: 8.5,
                maxBounds: caragaBounds,         
                maxBoundsViscosity: 1.0          
            }).setView([9.1500, 125.9000], 8.5); 

            const streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles &copy; Esri &mdash; Sources'
            });

            const baseLayers = {
                "Street View": streetLayer,
                "Satellite View": satelliteLayer
            };

            L.control.layers(baseLayers, null, { position: "topright" }).addTo(map);
            markerGroup = L.layerGroup().addTo(map);

            // Redraw ArcGIS overlay zones
            renderProvinceBoundariesSimulations();
        }


        function renderProvinceBoundariesSimulations() {
            const boundaries = [
                { name: "Regional Office Area", coord: [8.9515, 125.5350], radius: 6000, color: '#0F172A' },
                { name: "Butuan City Area", coord: [8.9475, 125.5406], radius: 12000, color: '#3B82F6' },
                { name: "Agusan del Norte Area", coord: [9.1204, 125.5342], radius: 25000, color: '#10B981' },
                { name: "Agusan del Sur Area", coord: [8.5994, 125.9189], radius: 45000, color: '#F59E0B' },
                { name: "Surigao del Norte Area", coord: [9.7891, 125.4958], radius: 30000, color: '#8B5CF6' },
                { name: "Surigao del Sur Area", coord: [9.0768, 126.1956], radius: 40000, color: '#EC4899' },
                { name: "Dinagat Islands Area", coord: [10.1264, 125.5744], radius: 20000, color: '#14B8A6' }
            ];

            boundaries.forEach(b => {
                L.circle(b.coord, {
                    color: b.color,
                    fillColor: b.color,
                    fillOpacity: 0.03,
                    weight: 1.5,
                    dashArray: '5, 8'
                }).addTo(map).bindTooltip(b.name, { sticky: true });
            });
        }

        // Draw course split metrics and spreadsheet matrices

        function refreshGISMapMarkers(trainings) {
            markerGroup.clearLayers();

            if (heatmapLayer) {
                map.removeLayer(heatmapLayer);
                heatmapLayer = null;
            }

            const heatPoints = [];

            trainings.forEach(t => {
                const color = STATUS_HEX_COLORS[t.status] || '#3B82F6';

                const iconMarkup = `
                    <div class="relative flex items-center justify-center">
                        <span class="absolute w-4.5 h-4.5 rounded-full opacity-35 animate-ping" style="background-color: ${color}"></span>
                        <span class="relative block w-3.5 h-3.5 rounded-full border-2 border-white shadow-xl" style="background-color: ${color}"></span>
                    </div>
                `;

                const divIcon = L.divIcon({
                    html: iconMarkup,
                    className: 'custom-pin-icon',
                    iconSize: [20, 20],
                    iconAnchor: [10, 10]
                });

                const marker = L.marker([t.latitude, t.longitude], { icon: divIcon });

                const popupContent = `
                    <div class="p-3 w-64 text-xs">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-2">
                            <span class="text-[9px] uppercase font-extrabold text-slate-400 font-mono tracking-widest">${t.course_code}</span>
                            <span class="px-2 py-0.5 text-[9px] rounded-full font-bold uppercase text-white" style="background-color: ${color}">
                                ${t.status}
                            </span>
                        </div>
                        <h4 class="font-extrabold text-slate-900 text-xs leading-tight mb-2">${t.training_title}</h4>
                        <div class="space-y-1.5 text-slate-600">
                            <div class="flex items-start gap-1">
                                <i class="fa-solid fa-location-dot mt-0.5 text-slate-400 w-3.5"></i>
                                <span>${t.venue}, ${t.municipality}, ${t.province}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <i class="fa-solid fa-calendar w-3.5 text-slate-400"></i>
                                <span>${t.start_date} to ${t.end_date}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <i class="fa-solid fa-clock w-3.5 text-slate-400"></i>
                                <span>Duration: <b>${t.duration_hours || 3} Hours</b> (${t.course_type || 'Webinar'})</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <i class="fa-solid fa-users w-3.5 text-slate-400"></i>
                                <span>Pax: M: ${t.male_participants || 0} | F: ${t.female_participants || 0} | T: ${t.actual_participants || 0}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <i class="fa-solid fa-wallet w-3.5 text-slate-400"></i>
                                <span class="font-semibold text-slate-800">Budget: ${formatCurrency(t.budget_allocated)}</span>
                            </div>
                        </div>
                        <div class="mt-3 pt-2 border-t border-slate-100 flex justify-between items-center text-[10px]">
                            <span class="font-semibold text-slate-500">Resource: ${t.resource_person}</span>
                        </div>
                        <div class="mt-2.5 flex items-center justify-between">
                            ${t.drive_link ? `<a href="${t.drive_link}" target="_blank" class="w-full text-center bg-blue-600 text-white font-bold py-1 px-2.5 rounded hover:bg-blue-700 transition-all text-[10px]"><i class="fa-solid fa-external-link-alt mr-1"></i> Open Google Drive Folder</a>` : '<span class="text-[9px] text-slate-400 italic">No drive documents linked</span>'}
                        </div>
                    </div>
                `;

                marker.bindPopup(popupContent);
                markerGroup.addLayer(marker);

                heatPoints.push([t.latitude, t.longitude, 0.7]);
            });

            heatmapLayer = L.heatLayer(heatPoints, { radius: 25, blur: 15, max: 1.0 });
            if (isHeatmapActive) {
                map.addLayer(heatmapLayer);
            }
        }

        // Toggle Heatmap Layer

        function toggleHeatmap() {
            isHeatmapActive = !isHeatmapActive;
            const btn = document.getElementById("heatmap-toggle-btn");
            if (isHeatmapActive) {
                btn.className = "text-xs px-3.5 py-1.5 border border-orange-600 rounded-lg bg-orange-500 text-white font-bold flex items-center gap-1.5 transition-all";
                if (heatmapLayer) map.addLayer(heatmapLayer);
            } else {
                btn.className = "text-xs px-3.5 py-1.5 border border-slate-300 rounded-lg bg-white text-slate-700 font-bold flex items-center gap-1.5 transition-all";
                if (heatmapLayer) map.removeLayer(heatmapLayer);
            }
        }

        // Render data rows in Tracker grid
