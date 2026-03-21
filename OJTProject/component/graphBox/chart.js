document.addEventListener("DOMContentLoaded", function () {
  const wrapper = document.getElementById("chartsWrapper");

  // Debugger para sa data
  console.log("Chart Data Received:", allChartData);

  if (
    !wrapper ||
    typeof allChartData === "undefined" ||
    Object.keys(allChartData).length === 0
  ) {
    console.warn("No data for charts found.");
    return;
  }

  // --- LAYOUT SETUP ---
  // Ginawa nating grid ang wrapper para bawat item ay may sariling "box"
  wrapper.style.display = "grid";
  wrapper.style.gridTemplateColumns = "repeat(auto-fit, minmax(320px, 1fr))";
  wrapper.style.gap = "10px";
  wrapper.innerHTML = ""; // Clear muna natin ang "No data" message kung meron

  const isMonthly = currentView === "monthly";

  // Loop sa lahat ng items na galing sa PHP
  Object.keys(allChartData).forEach((item) => {
    const chartDiv = document.createElement("div");
    chartDiv.className = "item-card";

    // Linisin ang ID para sa canvas
    const canvasId = `chart-${item.replace(/[^a-z0-9]/gi, "-")}`;

    // Layout ng bawat Box
    chartDiv.innerHTML = `
      <div class="item-info" style="border-bottom: 1px solid #eee; margin-bottom: 12px; padding-bottom: 6px;">
          <strong style="color: #072d7a; font-size: 1.1rem; display: block;">${item}</strong>
          <span style="font-size: 0.8rem; color: #777;">${allChartData[item].desc || "No description"}</span>
      </div>
      <div style="height:200px;"><canvas id="${canvasId}"></canvas></div>
    `;
    wrapper.appendChild(chartDiv);

    let labels, dataPoints;

    if (isMonthly) {
      // --- MONTHLY DATA ---
      const targetMonth =
        typeof currentMonth !== "undefined"
          ? currentMonth
          : new Date().getMonth() + 1;
      const targetYear =
        typeof currentYear !== "undefined"
          ? currentYear
          : new Date().getFullYear();
      const daysInMonth = new Date(targetYear, targetMonth, 0).getDate();

      labels = Array.from({ length: daysInMonth }, (_, i) => i + 1);
      const dateKeys = Array.from(
        { length: daysInMonth },
        (_, i) =>
          `${targetYear}-${String(targetMonth).padStart(2, "0")}-${String(i + 1).padStart(2, "0")}`,
      );
      dataPoints = dateKeys.map((key) => allChartData[item].stats[key] || 0);
    } else {
      // --- WEEKLY DATA ---
      labels = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
      dataPoints = allChartData[item].stats; // Ito yung array_fill(0,7,0) galing PHP
    }

    const ctx = document.getElementById(canvasId).getContext("2d");

    // "Interpretable" Logic:
    // Mag-re-red ang bar kung ang usage ay 10 pataas (Spike Detection)
    const backgroundColors = dataPoints.map((val) =>
      val >= 10 ? "#e74c3c" : "#3498db",
    );

    new Chart(ctx, {
      type: "bar",
      data: {
        labels: labels,
        datasets: [
          {
            label: "Usage",
            data: dataPoints,
            backgroundColor: backgroundColors,
            borderRadius: 4,
            borderWidth: 0,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false }, // Hindi na kailangan ang legend dahil per box ang item
          tooltip: {
            callbacks: {
              title: (tooltipItems) => {
                return isMonthly
                  ? `Day ${tooltipItems[0].label}`
                  : tooltipItems[0].label;
              },
            },
          },
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: { stepSize: 1, font: { size: 10 } },
            grid: { color: "#f3f3f3" },
          },
          x: {
            grid: { display: false },
            ticks: {
              font: { size: 9 },
              autoSkip: true, // Para hindi mag-overlap ang numbers sa Monthly (1-31)
              maxRotation: 0,
            },
          },
        },
      },
    });
  });
});
