// JS: AJAX + Chart update
const form = document.getElementById('predictForm');
const statusEl = document.getElementById('status');
const submitBtn = document.getElementById('submitBtn');

// init Chart.js (one time)
const ctx = document.getElementById('trendChart').getContext('2d');
const chart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: ['M-5','M-4','M-3','M-2','M-1','Now'], // change if needed
    datasets: [{ label: 'Popularity', data: [0,0,0,0,0,0], tension:0.3 }]
  },
  options: {}
});

function updateChart(series){
  if (!Array.isArray(series)) return;
  chart.data.datasets[0].data = series;
  chart.update();
}

form.addEventListener('submit', async (e) => {
  e.preventDefault();
  submitBtn.disabled = true;
  statusEl.textContent = 'Predicting...';

  const formData = new FormData(form);
  try {
    const resp = await fetch('predict.php', { method: 'POST', body: formData });
    const data = await resp.json();
    if (data.error) {
      statusEl.textContent = 'Error: ' + data.error;
    } else {
      const predText = data.prediction == 1 ? 'Trending' : 'Not Trending';
      const prob = data.probability ? (data.probability[1] ?? null) : null;
      statusEl.textContent = `${predText}` + (prob ? ` (prob ${Math.round(prob*100)}%)` : '');
      updateChart(data.trend_series || []);
    }
  } catch (err) {
    statusEl.textContent = 'Request failed';
    console.error(err);
  } finally {
    submitBtn.disabled = false;
  }
});
