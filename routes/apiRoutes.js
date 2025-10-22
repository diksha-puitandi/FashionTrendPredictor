import express from 'express';
import { getExternalData } from '../controllers/apiController.js';

const router = express.Router();

router.get('/data', getExternalData);

export default router;
