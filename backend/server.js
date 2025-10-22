import express from 'express';
import cors from 'cors';
import { PORT } from './config/apiConfig.js';
import apiRoutes from './routes/apiRoutes.js';

const app = express();

app.use(cors());
app.use(express.json());

app.use('/api', apiRoutes);

app.get('/', (req, res) => {
  res.send('✅ API Backend is running!');
});

app.listen(PORT, () => {
  console.log(`🚀 Server running at http://localhost:${PORT}`);
});
