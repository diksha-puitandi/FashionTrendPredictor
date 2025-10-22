// // Import dependencies
// import express from 'express';
// import fetch from 'node-fetch'; // or use global fetch in Node 18+

// const app = express();
// const PORT = process.env.PORT || 5000;

// // Middleware
// app.use(express.json());

// // Example route that fetches data from an external API
// app.get('/api/data', async (req, res) => {
//   try {
//     const response = await fetch('http://127.0.0.1:5000');
//     const data = await response.json();
//     res.json(data);
//   } catch (error) {
//     console.error('Error fetching data:', error);
//     res.status(500).json({ message: 'Failed to fetch data' });
//   }
// });

// // Start the server
// app.listen(PORT, () => {
//   console.log(`Server running on http://localhost:${PORT}`);
// });





import express from 'express';
import cors from 'cors';
import { PORT } from './config/apiConfig.js';
import apiRoutes from './routes/apiRoutes.js';

const app = express();

app.use(cors());
app.use(express.json());

// 👇 This mounts your routes at /api
app.use('/api', apiRoutes);

// Root route (so visiting http://localhost:5000 shows something)
app.get('/', (req, res) => {
  res.send('✅ API Backend is running!');
});

app.listen(PORT, () => {
  console.log(`🚀 Server running at http://localhost:${PORT}`);
});
